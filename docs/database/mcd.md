# MCD — Touche pas au klaxon

## Entités

### UTILISATEUR
- id_utilisateur
- nom
- prenom
- telephone
- email
- mot_de_passe
- role

---

### AGENCE
- id_agence
- nom

---

### TRAJET
- id_trajet
- date_depart
- date_arrivee
- places_total
- places_disponibles

---

## Relations

Un utilisateur peut proposer plusieurs trajets.

UTILISATEUR (0,N) ---- propose ---- (1,1) TRAJET


Une agence peut être utilisée comme agence de départ pour plusieurs trajets.

AGENCE (0,N) ---- depart_de ---- (1,1) TRAJET


Une agence peut être utilisée comme agence d’arrivée pour plusieurs trajets.

AGENCE (0,N) ---- arrivee_de ---- (1,1) TRAJET


## Description

Le modèle conceptuel de données met en évidence trois entités principales :

- Utilisateur : représente un employé pouvant proposer un trajet
- Agence : représente un site de l'entreprise (ville)
- Trajet : représente un déplacement entre deux agences

Chaque trajet possède :
- un auteur (utilisateur)
- une agence de départ
- une agence d’arrivée
- une date de départ et d’arrivée
- un nombre de places 