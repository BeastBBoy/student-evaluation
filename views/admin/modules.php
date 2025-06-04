<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-book me-2"></i>Gestion des Modules</h4>
                    <a href="index.php?page=modules&action=create" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Nouveau Module
                    </a>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($modules)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun module disponible.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Enseignant</th>
                                        <th>Date de création</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($modules as $module): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($module['nom']); ?></td>
                                            <td><?php echo htmlspecialchars(substr($module['description'], 0, 100)) . (strlen($module['description']) > 100 ? '...' : ''); ?></td>
                                            <td><?php echo htmlspecialchars($module['enseignant_nom']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($module['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?page=modules&action=view&id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="index.php?page=admin&action=editModule&id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="index.php?page=admin&action=deleteModule&id=<?php echo $module['id']; ?>" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>