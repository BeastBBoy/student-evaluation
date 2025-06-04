<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Modifier l'utilisateur</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Rôle</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="etudiant" <?php echo $user['role'] === 'etudiant' ? 'selected' : ''; ?>>Étudiant</option>
                                <option value="enseignant" <?php echo $user['role'] === 'enseignant' ? 'selected' : ''; ?>>Enseignant</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrateur</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="promotion" class="form-label">Promotion</label>
                            <input type="text" class="form-control" id="promotion" name="promotion" value="<?php echo htmlspecialchars($user['promotion']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="groupe" class="form-label">Groupe</label>
                            <input type="text" class="form-control" id="groupe" name="groupe" value="<?php echo htmlspecialchars($user['groupe']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="index.php?page=admin&action=users" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>