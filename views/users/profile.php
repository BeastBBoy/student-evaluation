<?php
// No output should be here before any potential header redirects
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user-circle me-2"></i>Mon Profil</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="index.php?page=profile">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom complet</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmer_mot_de_passe" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" id="confirmer_mot_de_passe" name="confirmer_mot_de_passe">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>