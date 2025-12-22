import fs from 'node:fs';
import path from 'node:path';
import process from 'node:process';
import { spawn } from 'node:child_process';

const watchedRoots = [
  'resources/views/components/ui',
  'resources/views/templates',
];

const debounceMs = 800;
let pendingTimer = null;
let isRunning = false;

function debounce(fn, ms) {
  return (...args) => {
    if (pendingTimer) {
      clearTimeout(pendingTimer);
    }
    pendingTimer = setTimeout(() => fn(...args), ms);
  };
}

function runRebuild() {
  if (isRunning) {
    return;
  }

  isRunning = true;
  console.log('[inventory] Rebuild caches...');

  const proc = spawn('php', ['artisan', 'inventory:cache:rebuild'], {
    stdio: 'inherit',
    shell: true,
  });

  proc.on('close', (code) => {
    isRunning = false;
    if (code === 0) {
      console.log('[inventory] OK');
    } else {
      console.error('[inventory] FAILED');
    }
  });
}

const debouncedRebuild = debounce(runRebuild, debounceMs);

async function listDirectoriesRecursively(rootDir) {
  const dirs = [rootDir];

  const stack = [rootDir];
  while (stack.length > 0) {
    const current = stack.pop();
    let entries = [];
    try {
      entries = await fs.promises.readdir(current, { withFileTypes: true });
    } catch {
      continue;
    }

    for (const entry of entries) {
      if (!entry.isDirectory()) {
        continue;
      }

      if (entry.name.startsWith('.')) {
        continue;
      }

      const full = path.join(current, entry.name);
      dirs.push(full);
      stack.push(full);
    }
  }

  return dirs;
}

function shouldTrigger(filePath) {
  if (!filePath) {
    return false;
  }

  const normalized = filePath.split(path.sep).join('/');

  if (!normalized.endsWith('.blade.php')) {
    return false;
  }

  if (normalized.includes('/partials/')) {
    return false;
  }

  return true;
}

async function watchRoot(root) {
  const absRoot = path.resolve(process.cwd(), root);

  if (!fs.existsSync(absRoot)) {
    return;
  }

  // Try recursive watch (works on macOS/Windows). If unsupported, we fallback to watching directories one by one.
  try {
    fs.watch(absRoot, { recursive: true }, (_event, filename) => {
      const filePath = filename ? path.join(absRoot, filename) : null;
      if (shouldTrigger(filePath)) {
        console.log(`[inventory] change: ${filePath}`);
        debouncedRebuild();
      }
    });

    console.log(`[inventory] watching (recursive): ${root}`);

    return;
  } catch {
    // Fallback below.
  }

  const dirs = await listDirectoriesRecursively(absRoot);
  for (const dir of dirs) {
    try {
      fs.watch(dir, { recursive: false }, (_event, filename) => {
        const filePath = filename ? path.join(dir, filename) : null;
        if (shouldTrigger(filePath)) {
          console.log(`[inventory] change: ${filePath}`);
          debouncedRebuild();
        }
      });
    } catch {
      // Ignore directories that cannot be watched.
    }
  }

  console.log(`[inventory] watching (${dirs.length} dirs): ${root}`);
}

console.log('[inventory] watch started');
await Promise.all(watchedRoots.map(watchRoot));


