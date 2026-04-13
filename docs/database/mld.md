# MLD — Touche pas au klaxon

## Tables

### UTILISATEURS

| champ | type logique |
|------|-------------|
| id | PK |
| nom | string |
| prenom | string |
| telephone | string |
| email | string UNIQUE |
| mot_de_passe | string |
| role | string |

---

### AGENCES

| champ | type logique |
|------|-------------|
| id | PK |
| nom | string UNIQUE |

---

### TRAJETS

| champ | type logique |
|------|-------------|
| id | PK |
| auteur_id | FK → utilisateurs.id |
| agence_depart_id | FK → agences.id |
| agence_arrivee_id | FK → agences.id |
| date_depart | datetime |
| date_arrivee | datetime |
| places_total | int |
| places_disponibles | int |

---

## Relations

TRAJETS.auteur_id → UTILISATEURS.id

TRAJETS.agence_depart_id → AGENCES.id

TRAJETS.agence_arrivee_id → AGENCES.id

---

## Contraintes métier

- email utilisateur unique
- nom agence unique
- agence_depart différente de agence_arrivee
- date_arrivee > date_depart
- places_total > 0
- places_disponibles >= 0
- places_disponibles <= places_total