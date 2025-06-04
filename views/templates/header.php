<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Style personnalisé -->
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/style.css">
</head>
<body>
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php?page=home">
                <i class="fas fa-graduation-cap me-2"></i><?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=home"><i class="fas fa-home me-1"></i> Accueil</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=evaluations&action=listQuizzes"><i class="fas fa-tasks me-1"></i> Évaluations</a>
                        </li>
                        <!-- Teacher dashboard link only for teachers -->
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'enseignant'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=teacher&action=dashboard">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> Espace Enseignant
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Admin dashboard link only for admins -->
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="index.php?page=admin&action=dashboard">
                                    <i class="fas fa-cogs me-1"></i> Administration
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="index.php?page=profile"><i class="fas fa-id-card me-1"></i> Mon profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="index.php?page=auth&action=logout"><i class="fas fa-sign-out-alt me-1"></i> Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=auth&action=login"><i class="fas fa-sign-in-alt me-1"></i> Connexion</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?page=auth&action=register"><i class="fas fa-user-plus me-1"></i> Inscription</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mb-4">