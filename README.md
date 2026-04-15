# Touche pas au klaxon

Application web interne permettant de favoriser le covoiturage entre les
différents sites d'une entreprise.

Le site permet aux employés de publier leurs trajets professionnels afin
d'optimiser le taux de remplissage des véhicules et réduire le nombre de
trajets effectués avec un seul occupant.

------------------------------------------------------------------------

# Fonctionnalités

## Utilisateur non connecté

-   consultation de la liste des trajets disponibles
-   tri par date de départ croissante
-   affichage uniquement des trajets ayant des places disponibles

## Utilisateur connecté

-   consulter le détail d'un trajet
-   créer un trajet
-   modifier ses trajets
-   supprimer ses trajets
-   réserver une place
-   annuler une réservation
-   consulter ses réservations

## Administrateur

-   accès au dashboard
-   lister les utilisateurs
-   lister les agences
-   créer une agence
-   modifier une agence
-   supprimer une agence
-   consulter les trajets
-   supprimer un trajet

------------------------------------------------------------------------

# Architecture

L'application suit une architecture MVC.

## Model

Responsable de l'accès aux données via PDO.

Exemples : - Trip - Agency - Reservation - User

## View

Responsable de l'affichage HTML.

Organisation : - home - trips - admin - layouts

## Controller

Coordonne les actions entre les modèles et les vues.

Exemples : - HomeController - TripController - AuthController -
AdminController

## Services

Contiennent la logique métier.

Exemple : - TripService

Avantages : - code maintenable - meilleure testabilité - séparation des
responsabilités

------------------------------------------------------------------------

# Structure du projet

touche-pas-au-klaxon/

app/ Controllers/ Models/ Services/ Views/ Core/ Helpers/ Config/

database/ 001_init.sql 002_seed.sql

public/ index.php

routes/ web.php

scripts/

tests/

phpstan.neon phpunit.xml

------------------------------------------------------------------------

# Base de données

Base relationnelle MySQL.

Tables principales :

## utilisateurs

contient les employés

## agences

contient les villes

## trajets

trajets proposés

## reservations

réservations des utilisateurs

Relations :

-   un utilisateur peut créer plusieurs trajets
-   un trajet appartient à un utilisateur
-   un trajet possède une agence de départ
-   un trajet possède une agence d'arrivée
-   un utilisateur peut réserver plusieurs trajets
-   une réservation appartient à un utilisateur et un trajet

------------------------------------------------------------------------

# Prérequis

-   PHP \>= 8.2
-   MySQL \>= 8
-   Composer
-   XAMPP ou équivalent
-   PHPUnit
-   PHPStan

------------------------------------------------------------------------

# Installation locale

## 1. Copier le projet

C:`\xampp`{=tex}`\htdocs`{=tex}`\touche`{=tex}-pas-au-klaxon

## 2. Installer les dépendances

composer install

## 3. Créer la base de données

Créer une base :

touche_pas_au_klaxon

Importer les scripts SQL :

database/001_init.sql

database/002_seed.sql

Commande possible :

cmd /c "C:`\xampp`{=tex}`\mysql`{=tex}`\bin`{=tex}`\mysql`{=tex}.exe -u
root \< database\\001_init.sql"

cmd /c "C:`\xampp`{=tex}`\mysql`{=tex}`\bin`{=tex}`\mysql`{=tex}.exe -u
root \< database\\002_seed.sql"

## 4. Configuration

Modifier :

app/Config/config.php

exemple :

return \[\];

## 5. Lancer le projet

Démarrer Apache et MySQL.

Accéder :

http://localhost/touche-pas-au-klaxon/public

------------------------------------------------------------------------

# Comptes de test

## Admin

email : admin@email.fr

mot de passe : password

## Utilisateur

email : alexandre.martin@email.fr

mot de passe : password

------------------------------------------------------------------------

# Scripts

Les scripts PowerShell permettent de générer automatiquement les
fichiers.

Dossier :

scripts/

Exécution :

powershell -ExecutionPolicy Bypass -File
.`\scripts`{=tex}`\nom`{=tex}\_script.ps1

------------------------------------------------------------------------

# Tests PHPUnit

Lancer les tests :

vendor`\bin`{=tex}`\phpunit`{=tex}

Résultat attendu :

OK

Les tests couvrent :

-   création trajet
-   modification trajet
-   suppression trajet
-   réservation
-   annulation réservation

------------------------------------------------------------------------

# PHPStan

Analyse statique :

composer stan

Résultat attendu :

\[OK\] No errors

------------------------------------------------------------------------

# Sécurité

-   validation des entrées
-   requêtes préparées PDO
-   authentification par session
-   contrôle des permissions
-   messages flash
-   typage strict

------------------------------------------------------------------------

# Qualité du code

-   architecture MVC
-   services métier
-   DocBlock
-   PHPStan
-   PHPUnit

------------------------------------------------------------------------

# Workflow

1.  création issue
2.  création branche
3.  développement
4.  commit
5.  pull request
6.  merge

------------------------------------------------------------------------

# Auteur

Projet réalisé dans le cadre de la formation Développeur Web et Web
Mobile.
