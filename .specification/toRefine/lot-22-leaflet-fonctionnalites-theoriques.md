# Lot 22 : Leaflet - liste des fonctionnalites theoriquement disponibles

## Contexte
- Le composant `leaflet` ne fonctionne plus de maniere fiable.
- Ce lot a pour seul but de dresser la liste des fonctionnalites qu'il est cense proposer.
- Ce document ne decrit ni l'implementation technique ni la maniere dont chaque fonctionnalite est obtenue.

## Fonctionnalites theoriquement disponibles

### Carte de base
- Affichage d'une carte Leaflet.
- Definition d'un centre initial via latitude et longitude.
- Definition d'un niveau de zoom initial.
- Definition d'un zoom minimum.
- Definition d'un zoom maximum.
- Personnalisation de l'identifiant du conteneur.
- Personnalisation des classes CSS du conteneur.
- Personnalisation du style inline du conteneur.

### Fond de carte
- Utilisation d'un provider de tuiles.
- Utilisation d'une URL de tuiles personnalisee.
- Passage d'options de configuration pour les tuiles.

### Affichage et dimensionnement
- Hauteur par defaut si aucune hauteur n'est definie.
- Utilisation de classes de hauteur personnalisees.
- Affichage dans des mises en page responsives.
- Utilisation dans des onglets.

### Controles de navigation
- Gestion des gestes utilisateur.
- Geolocalisation utilisateur.
- Mode plein ecran.
- Synchronisation avec l'URL.
- Affichage d'une echelle.

### Donnees affichees
- Ajout de marqueurs simples.
- Ajout de marqueurs avec configuration avancee.
- Affichage de popups HTML.
- Affichage de donnees GeoJSON.

### Couches et visualisations avancees
- Regroupement de marqueurs.
- Configuration des options de regroupement.
- Affichage d'une heatmap.
- Affichage d'une mini-carte.

### Edition et mesure
- Outils de dessin sur la carte.
- Outils d'edition des formes.
- Outils de mesure.

### Recherche et services cartographiques
- Geocodage.
- Choix du provider de geocodage.
- Itineraires.

### Etats visuels du composant
- Etat de chargement.
- Etat d'erreur.

## Liste theorique des props exposees

### Structure
- `id`
- `class`
- `style`
- `module`

### Vue initiale
- `lat`
- `lng`
- `zoom`
- `minZoom`
- `maxZoom`
- `preferCanvas`

### Tuiles
- `provider`
- `tileUrl`
- `tileOptions`

### Controles et plugins
- `gestureHandling`
- `locateControl`
- `fullscreen`
- `hash`
- `scale`
- `cluster`
- `clusterOptions`
- `heatmap`
- `miniMap`
- `draw`
- `measure`
- `geocoder`
- `routing`

### Donnees
- `markers`
- `geojson`

## Resultat attendu pour ce lot
- Une liste claire et concise des fonctionnalites theoriquement disponibles.
- Aucune explication technique sur leur mise en oeuvre.
