-- Création de la base de données
CREATE DATABASE IF NOT EXISTS yitro_learning
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

-- Utilisation de la base de données
USE yitro_learning;

-- Création de la table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    photo VARCHAR(255),
    pays VARCHAR(100),
    langue VARCHAR(100),
    autre_langue VARCHAR(100),
    objectifs TEXT,
    type_cours VARCHAR(100),
    niveau_formation VARCHAR(100),
    niveau_etude VARCHAR(100),
    acces_internet VARCHAR(50),
    appareil VARCHAR(100),
    accessibilite TEXT,
    rgpd TINYINT(1) DEFAULT 0,
    charte TINYINT(1) DEFAULT 0,
    role ENUM('apprenant', 'admin', 'moderator') DEFAULT 'apprenant',
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table formateurs
CREATE TABLE IF NOT EXISTS formateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    ville_pays VARCHAR(255),
    linkedin VARCHAR(255),
    intitule_metier VARCHAR(255),
    experience_formation TEXT,
    detail_experience TEXT,
    cv VARCHAR(255),
    categories TEXT,
    autre_domaine VARCHAR(255),
    titre_cours VARCHAR(255),
    objectif TEXT,
    public_cible TEXT,
    detail_complementaire TEXT,
    formats TEXT,
    format_autre VARCHAR(255),
    duree_estimee VARCHAR(100),
    type_formation VARCHAR(100),
    motivation TEXT,
    valeurs TEXT,
    profil_public TEXT,
    statut ENUM('en_attente', 'verifie', 'premium', 'partenaire') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ajout d'un administrateur par défaut (mot de passe : "admin123")
INSERT INTO utilisateurs (nom, email, mot_de_passe, role, actif)
VALUES (
    'Admin',
    'litomanto2@gmail.com',
    '$2y$10$mvmghyfCcp5ziWaLnzy.BuCq0bmE5B.gttWfz1X///O0NknyhT6gq', 
    'admin',
    1
);

-- Table des cours
CREATE TABLE IF NOT EXISTS cours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    formateur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formateur_id) REFERENCES formateurs(id) ON DELETE CASCADE
);

-- Table des modules
CREATE TABLE IF NOT EXISTS modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
);

-- Table des leçons
CREATE TABLE IF NOT EXISTS lecons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    format ENUM('pdf', 'audio', 'video') NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS journal_activite (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


ALTER TABLE formateurs ADD code_entree VARCHAR(255) NULL;


ALTER TABLE formateurs ADD password VARCHAR(255) DEFAULT NULL;

ALTER TABLE cours ADD photo VARCHAR(255) NULL AFTER prix;

-- Ajout de la table contact
CREATE TABLE IF NOT EXISTS contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table forum
CREATE TABLE IF NOT EXISTS forum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cours_id) REFERENCES cours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table post
CREATE TABLE IF NOT EXISTS post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auteur_id INT NOT NULL,
    forum_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_post TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (forum_id) REFERENCES forum(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE completions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    module_id INT NOT NULL,
    cours_id INT NOT NULL,
    date_completion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (module_id) REFERENCES modules(id),
    FOREIGN KEY (cours_id) REFERENCES cours(id),
    UNIQUE (utilisateur_id, module_id)
);

CREATE TABLE inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    cours_id INT NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut_paiement ENUM('en_attente', 'paye', 'echec') DEFAULT 'en_attente',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (cours_id) REFERENCES cours(id),
    UNIQUE (utilisateur_id, cours_id)
);

-- Création de la table pour les quiz
CREATE TABLE IF NOT EXISTS quiz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    module_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    description TEXT,
    score_minimum INT NOT NULL DEFAULT 70, -- Score minimum en pourcentage
    FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
);

-- Création de la table pour les questions
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    texte TEXT NOT NULL,
    reponse_correcte VARCHAR(255) NOT NULL,
    reponse_incorrecte_1 VARCHAR(255) NOT NULL,
    reponse_incorrecte_2 VARCHAR(255) NOT NULL,
    reponse_incorrecte_3 VARCHAR(255) NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
);

-- Création de la table pour les résultats des quiz
CREATE TABLE IF NOT EXISTS resultats_quiz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL, -- Score en pourcentage
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
);

CREATE TABLE lecons_completees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    lecon_id INT,
    date_completion DATETIME,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (lecon_id) REFERENCES lecons(id)
);
CREATE TABLE forum_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cours_id INT,
    utilisateur_id INT,
    message TEXT,
    date DATETIME,
    lu BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (cours_id) REFERENCES cours(id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

