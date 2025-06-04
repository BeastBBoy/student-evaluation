<?php
// Configuration générale de l'application

// Informations sur le site
define('SITE_NAME', 'Évaluations En Ligne');
define('SITE_URL', 'http://localhost/Newproject-ahmed');

// Configuration des chemins
define('ASSETS_URL', SITE_URL . '/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');

// Configuration des sessions
// Moved to index.php before session_start()
// ini_set('session.cookie_lifetime', 3600); 
// ini_set('session.gc_maxlifetime', 3600);

// Fuseau horaire
date_default_timezone_set('Europe/Paris');

// Initialisation de la base de données
require_once ROOT_PATH . '/config/database.php';
setupDatabase();