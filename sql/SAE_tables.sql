DROP TABLE IF EXISTS billet;
DROP TABLE IF EXISTS commande;
DROP TABLE IF EXISTS tarif;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS image;
DROP TABLE IF EXISTS spectacle_artiste;
DROP TABLE IF EXISTS artiste;
DROP TABLE IF EXISTS spectacle;
DROP TABLE IF EXISTS style;
DROP TABLE IF EXISTS soiree;
DROP TABLE IF EXISTS lieu;

CREATE TABLE lieu (
    id CHAR(36) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    nb_places INT NOT NULL
);

CREATE TABLE soiree (
    id CHAR(36) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    thematique VARCHAR(50) NOT NULL,
    date_soiree DATE NOT NULL,
    heure_debut TIME NOT NULL,
    places_restantes INT NOT NULL,
    id_lieu CHAR(36) NOT NULL,
    FOREIGN KEY (id_lieu) REFERENCES lieu(id)
);

CREATE TABLE style (
    id CHAR(36) PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE spectacle (
    id CHAR(36) PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    url_video VARCHAR(255),
    horaire TIME,
    id_soiree CHAR(36) NOT NULL,
    id_style CHAR(36) NOT NULL,
    FOREIGN KEY (id_soiree) REFERENCES soiree(id),
    FOREIGN KEY (id_style) REFERENCES style(id)
);

CREATE TABLE artiste (
    id CHAR(36) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
);

CREATE TABLE spectacle_artiste (
    id_spectacle CHAR(36) NOT NULL,
    id_artiste CHAR(36) NOT NULL,
    PRIMARY KEY (id_spectacle, id_artiste),
    FOREIGN KEY (id_spectacle) REFERENCES spectacle(id) ON DELETE CASCADE,
    FOREIGN KEY (id_artiste) REFERENCES artiste(id) ON DELETE CASCADE
);

CREATE TABLE image (
    id CHAR(36) PRIMARY KEY,
    url VARCHAR(255) NOT NULL,
    id_spectacle CHAR(36) NOT NULL,
    FOREIGN KEY (id_spectacle) REFERENCES spectacle(id) ON DELETE CASCADE
);

CREATE TABLE utilisateur (
    id CHAR(36) PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role TINYINT NOT NULL DEFAULT 0 -- 0 = client, 1 = admin
);

CREATE TABLE tarif (
    id CHAR(36) PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL UNIQUE,
    prix DECIMAL(8,2) NOT NULL
);

CREATE TABLE commande (
    id CHAR(36) PRIMARY KEY,
    id_utilisateur CHAR(36) NOT NULL,
    date_commande DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant_total DECIMAL(8,2) NOT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT 'en_attente',
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id)
);

CREATE TABLE billet (
    id CHAR(36) PRIMARY KEY,
    reference VARCHAR(255) NOT NULL UNIQUE,
    token VARCHAR(255) NOT NULL UNIQUE,
    id_commande CHAR(36) NOT NULL,
    id_soiree CHAR(36) NOT NULL,
    id_tarif CHAR(36) NOT NULL,
    prix_paye DECIMAL(8,2) NOT NULL,
    est_pass BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (id_commande) REFERENCES commande(id),
    FOREIGN KEY (id_soiree) REFERENCES soiree(id),
    FOREIGN KEY (id_tarif) REFERENCES tarif(id)
);