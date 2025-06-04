<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier l'Évaluation</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre de l'évaluation</label>
                            <input type="text" class="form-control" id="titre" name="titre" value="<?php echo htmlspecialchars($evaluation['titre']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($evaluation['description']); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="module_id" class="form-label">Module</label>
                            <select class="form-select" id="module_id" name="module_id" required>
                                <?php foreach ($modules as $module): ?>
                                    <option value="<?php echo $module['id']; ?>" <?php echo ($module['id'] == $evaluation['module_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($module['nom_complet']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="duree" class="form-label">Durée (minutes)</label>
                            <input type="number" class="form-control" id="duree" name="duree" value="<?php echo htmlspecialchars($evaluation['duree']); ?>" required>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php?page=admin&action=evaluations" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>