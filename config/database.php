<?php
// Configuration de la base de données

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'evaluation_en_ligne');

// Fonction pour établir une connexion à la base de données
function getDbConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        // Configurer PDO pour qu'il lance des exceptions en cas d'erreur
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Configurer PDO pour qu'il retourne les résultats sous forme d'objets
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch(PDOException $e) {
        die("Erreur de connexion à la base de données: " . $e->getMessage());
    }
}

// Fonction pour créer la base de données et les tables si elles n'existent pas
function setupDatabase() {
    try {
        // Connexion sans sélectionner de base de données
        $conn = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer la base de données si elle n'existe pas
        $conn->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $conn->exec("USE " . DB_NAME);
        
        // Créer la table utilisateurs
        $conn->exec("CREATE TABLE IF NOT EXISTS utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            date_naissance DATE NOT NULL,
            promotion VARCHAR(10) NOT NULL,
            groupe VARCHAR(10) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            mot_de_passe VARCHAR(255) NOT NULL,
            role ENUM('etudiant', 'enseignant') NOT NULL DEFAULT 'etudiant',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Créer la table modules
        $conn->exec("CREATE TABLE IF NOT EXISTS modules (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(100) NOT NULL,
            description TEXT,
            enseignant_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (enseignant_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
        )");
        
        // Créer la table evaluations
        $conn->exec("CREATE TABLE IF NOT EXISTS evaluations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            titre VARCHAR(100) NOT NULL,
            description TEXT,
            module_id INT NOT NULL,
            date_debut DATETIME NOT NULL,
            date_fin DATETIME NOT NULL,
            duree INT NOT NULL, -- durée en minutes
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
        )");
        
        // Créer la table questions
        $conn->exec("CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            evaluation_id INT NOT NULL,
            texte TEXT NOT NULL,
            type ENUM('qcm', 'texte') NOT NULL,
            points INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
        )");
        
        // Créer la table options (pour les questions QCM)
        $conn->exec("CREATE TABLE IF NOT EXISTS options (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT NOT NULL,
            texte TEXT NOT NULL,
            est_correcte BOOLEAN NOT NULL DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
        )");
        
        // Créer la table resultats
        $conn->exec("CREATE TABLE IF NOT EXISTS resultats (
            id INT AUTO_INCREMENT PRIMARY KEY,
            etudiant_id INT NOT NULL,
            evaluation_id INT NOT NULL,
            score FLOAT NOT NULL,
            date_soumission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
            FOREIGN KEY (evaluation_id) REFERENCES evaluations(id) ON DELETE CASCADE
        )");
        
        // Créer la table reponses
        $conn->exec("CREATE TABLE IF NOT EXISTS reponses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            resultat_id INT NOT NULL,
            question_id INT NOT NULL,
            option_id INT,
            texte_reponse TEXT,
            est_correcte BOOLEAN,
            points_obtenus FLOAT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (resultat_id) REFERENCES resultats(id) ON DELETE CASCADE,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
            FOREIGN KEY (option_id) REFERENCES options(id) ON DELETE SET NULL
        )");
        
       
        
    } catch(PDOException $e) {
        die("Erreur lors de la configuration de la base de données: " . $e->getMessage());
    }
}