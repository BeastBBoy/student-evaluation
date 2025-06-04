<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-book me-2"></i>Liste des modules</h4>
                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'enseignant' || $_SESSION['user_role'] === 'admin')): ?>
                        <a href="index.php?page=modules&action=create" class="btn btn-light btn-sm">
                            <i class="fas fa-plus-circle me-2"></i>Créer un module
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($modules)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun module disponible pour le moment.
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($modules as $module): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($module['nom']); ?></h5>
                                            <h6 class="card-subtitle mb-2 text-muted">
                                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($module['enseignant_nom']); ?>
                                            </h6>
                                            <p class="card-text"><?php echo htmlspecialchars($module['description']); ?></p>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="index.php?page=modules&action=view&id=<?php echo $module['id']; ?>" class="btn btn-primary btn-sm w-100">
                                                <i class="fas fa-eye me-1"></i>Voir le détail
                                            </a>
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $module['enseignant_id']): ?>
                                                <div class="btn-group w-100 mt-2">
                                                    <a href="index.php?page=modules&action=edit&id=<?php echo $module['id']; ?>" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-edit me-1"></i>Modifier
                                                    </a>
                                                    <a href="index.php?page=modules&action=delete&id=<?php echo $module['id']; ?>" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash me-1"></i>Supprimer
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>