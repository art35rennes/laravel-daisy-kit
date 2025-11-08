{{--
  Sous-composant/partial pour le rendu interne d'un évènement du Calendar Full.
  Le JS du calendrier recherche ces <template> par data-calendar-event:
  - chip : rendu compact utilisé dans la vue mois
  - block: rendu d'un bloc horaire (vues semaine/jour)
  - list : rendu pour la vue liste

  Placeholders disponibles (remplacés côté JS):
  - {{title}}          : Titre de l'évènement
  - {{date}}           : Date lisible (ex: mer. 13 août)
  - {{startTime}}      : Heure début (ex: 10:30)
  - {{endTime}}        : Heure fin (ex: 12:30)
  - {{timeRange}}      : Plage horaire formatée (selon allDay)
  - {{dotColor}}       : Couleur CSS pour le badge/dot
--}}

@verbatim
  <template data-calendar-event="chip">
      <span class="inline-flex items-center gap-1.5 truncate">
          <span class="cf-dot" style="background: {{dotColor}}"></span>
          <span class="truncate">{{title}}</span>
      </span>
      <!-- L'enveloppe (lien .cf-event) est fournie par le JS -->
  </template>

  <template data-calendar-event="block">
      <div class="flex items-center gap-2">
          <span class="cf-dot" style="background: {{dotColor}}"></span>
          <div class="min-w-0">
              <div class="font-medium leading-tight truncate">{{title}}</div>
              <div class="text-xs opacity-80 leading-tight">{{timeRange}}</div>
          </div>
      </div>
  </template>

  <template data-calendar-event="list">
      <div class="flex items-center gap-3 w-full">
          <span class="cf-dot" style="background: {{dotColor}}"></span>
          <div class="min-w-0">
              <div class="font-medium truncate">{{title}}</div>
              <div class="text-xs opacity-70">{{timeRange}}</div>
          </div>
      </div>
  </template>
@endverbatim


