CREATE DATABASE IF NOT EXISTS touche_pas_au_klaxon;
USE touche_pas_au_klaxon;

-- =====================
-- TABLE UTILISATEURS
-- =====================

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================
-- TABLE AGENCES
-- =====================

CREATE TABLE agences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- =====================
-- TABLE TRAJETS
-- =====================

CREATE TABLE trajets (
    id INT AUTO_INCREMENT PRIMARY KEY,

    auteur_id INT NOT NULL,

    agence_depart_id INT NOT NULL,
    agence_arrivee_id INT NOT NULL,

    date_depart DATETIME NOT NULL,
    date_arrivee DATETIME NOT NULL,

    places_total INT NOT NULL,
    places_disponibles INT NOT NULL,

    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_trajet_utilisateur
        FOREIGN KEY (auteur_id)
        REFERENCES utilisateurs(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_trajet_agence_depart
        FOREIGN KEY (agence_depart_id)
        REFERENCES agences(id),

    CONSTRAINT fk_trajet_agence_arrivee
        FOREIGN KEY (agence_arrivee_id)
        REFERENCES agences(id),

    CONSTRAINT chk_places_total
        CHECK (places_total > 0),

    CONSTRAINT chk_places_disponibles
        CHECK (places_disponibles >= 0),

    CONSTRAINT chk_places_coherence
        CHECK (places_disponibles <= places_total),

    CONSTRAINT chk_dates
        CHECK (date_arrivee > date_depart),

    CONSTRAINT chk_agences_differentes
        CHECK (agence_depart_id <> agence_arrivee_id)
);

-- =====================
-- INSERT AGENCES
-- =====================

INSERT INTO agences (nom) VALUES
('Paris'),
('Lyon'),
('Marseille'),
('Toulouse'),
('Nice'),
('Nantes'),
('Strasbourg'),
('Montpellier'),
('Bordeaux'),
('Lille'),
('Rennes'),
('Reims');

-- =====================
-- INSERT UTILISATEURS
-- mot de passe par défaut : password
-- hash généré avec password_hash()
-- =====================

INSERT INTO utilisateurs (nom, prenom, telephone, email, mot_de_passe, role) VALUES
('Martin','Alexandre','0612345678','alexandre.martin@email.fr','$2y$10$abcdefghijklmnopqrstuv','admin'),
('Dubois','Sophie','0698765432','sophie.dubois@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Bernard','Julien','0622446688','julien.bernard@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Moreau','Camille','0611223344','camille.moreau@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Lefèvre','Lucie','0777889900','lucie.lefevre@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Leroy','Thomas','0655443322','thomas.leroy@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Roux','Chloé','0633221199','chloe.roux@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Petit','Maxime','0766778899','maxime.petit@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Garnier','Laura','0688776655','laura.garnier@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Dupuis','Antoine','0744556677','antoine.dupuis@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Lefebvre','Emma','0699887766','emma.lefebvre@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Fontaine','Louis','0655667788','louis.fontaine@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Chevalier','Clara','0788990011','clara.chevalier@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Robin','Nicolas','0644332211','nicolas.robin@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Gauthier','Marine','0677889922','marine.gauthier@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Fournier','Pierre','0722334455','pierre.fournier@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Girard','Sarah','0688665544','sarah.girard@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Lambert','Hugo','0611223366','hugo.lambert@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Masson','Julie','0733445566','julie.masson@email.fr','$2y$10$abcdefghijklmnopqrstuv','user'),
('Henry','Arthur','0666554433','arthur.henry@email.fr','$2y$10$abcdefghijklmnopqrstuv','user');

-- =====================
-- JEU D'ESSAI TRAJETS
-- =====================

INSERT INTO trajets (
    auteur_id,
    agence_depart_id,
    agence_arrivee_id,
    date_depart,
    date_arrivee,
    places_total,
    places_disponibles
) VALUES
(1, 2, 1, '2026-05-01 08:00:00', '2026-05-01 12:00:00', 4, 3),
(2, 1, 3, '2026-05-02 09:00:00', '2026-05-02 13:30:00', 3, 2),
(3, 4, 2, '2026-05-03 07:30:00', '2026-05-03 11:00:00', 2, 2);