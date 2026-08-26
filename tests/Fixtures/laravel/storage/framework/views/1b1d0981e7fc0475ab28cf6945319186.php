


  <template data-calendar-event="chip">
      <span class="inline-flex items-center gap-1.5 truncate">
          <input type="color" value="{{dotColor}}" disabled class="cf-dot" aria-hidden="true" tabindex="-1">
          <span class="truncate">{{title}}</span>
      </span>
      <!-- L'enveloppe (lien .cf-event) est fournie par le JS -->
  </template>

  <template data-calendar-event="block">
      <div class="flex items-center gap-2">
          <input type="color" value="{{dotColor}}" disabled class="cf-dot" aria-hidden="true" tabindex="-1">
          <div class="min-w-0">
              <div class="font-medium leading-tight truncate">{{title}}</div>
              <div class="text-xs opacity-80 leading-tight">{{timeRange}}</div>
          </div>
      </div>
  </template>

  <template data-calendar-event="list">
      <div class="flex items-center gap-3 w-full">
          <input type="color" value="{{dotColor}}" disabled class="cf-dot" aria-hidden="true" tabindex="-1">
          <div class="min-w-0">
              <div class="font-medium truncate">{{title}}</div>
              <div class="text-xs opacity-70">{{timeRange}}</div>
          </div>
      </div>
  </template>

<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/partials/calendar-event.blade.php ENDPATH**/ ?>