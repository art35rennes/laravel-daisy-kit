import '../css/tree.css';
import { createMountable } from './core/mountable.js';
import { initialize } from './tree/runtime.js';

export const { getInstance, mount, mountAll, unmount } = createMountable('tree', initialize);
