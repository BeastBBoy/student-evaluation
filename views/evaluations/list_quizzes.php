<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-question-circle me-2"></i>Quizzes disponibles</h4>
                </div>
                <div class="card-body">
                    <!-- Module selection section with improved UI -->
                    <div class="mb-4">
                        <h5 class="mb-3"><i class="fas fa-book me-2"></i>Sélectionnez un module</h5>
                        
                        <!-- Search and filter bar -->
                        <div class="mb-3">
                            <div class="input-group">
                                <input type="text" id="moduleSearch" class="form-control" placeholder="Rechercher un module..." aria-label="Rechercher un module">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Module cards -->
                        <div class="row" id="moduleCards">
                            <?php foreach ($modules as $module): ?>
                                <div class="col-md-4 mb-3 module-card" data-module-name="<?php echo strtolower(htmlspecialchars($module['nom'])); ?>">
                                    <div class="card h-100 <?php echo (isset($_GET['module_id']) && $_GET['module_id'] == $module['id']) ? 'border-primary' : ''; ?>">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($module['nom']); ?></h5>
                                            <p class="card-text small text-muted"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($module['enseignant_nom']); ?></p>
                                            <?php if (!empty($module['description'])): ?>
                                                <p class="card-text small"><?php echo htmlspecialchars(substr($module['description'], 0, 60)) . (strlen($module['description']) > 60 ? '...' : ''); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-white">
                                            <a href="index.php?page=evaluations&action=listQuizzes&module_id=<?php echo $module['id']; ?>" class="btn btn-primary w-100">
                                                <i class="fas fa-list-alt me-2"></i>Voir les quizzes
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- No modules found message -->
                        <div id="noModulesFound" class="alert alert-info d-none">
                            <i class="fas fa-info-circle me-2"></i>Aucun module ne correspond à votre recherche.
                        </div>
                    </div>
                    
                    <!-- Selected module info and quizzes -->
                    <?php if (isset($_GET['module_id']) && $selected_module): ?>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5><i class="fas fa-book me-2"></i>Module: <?php echo htmlspecialchars($selected_module['nom']); ?></h5>
                                <a href="index.php?page=evaluations&action=listQuizzes" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Retour à la liste des modules
                                </a>
                            </div>
                            <p class="text-muted"><i class="fas fa-user me-1"></i>Enseignant: <?php echo htmlspecialchars($selected_module['enseignant_nom']); ?></p>
                            <?php if (!empty($selected_module['description'])): ?>
                                <p><?php echo htmlspecialchars($selected_module['description']); ?></p>
                            <?php endif; ?>
                            <hr>
                        </div>
                        
                        <?php if (empty($quizzes)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Aucun quiz n'est disponible pour ce module. Veuillez vérifier ultérieurement.
                            </div>
                        <?php else: ?>
                            <h5 class="mb-3"><i class="fas fa-question-circle me-2"></i>Quizzes disponibles (<?php echo count($quizzes); ?>)</h5>
                            <div class="row">
                                <?php foreach ($quizzes as $quiz): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card h-100 <?php echo in_array($quiz['id'], $taken_quizzes) ? 'border-success' : ''; ?>">
                                            <div class="card-header <?php echo in_array($quiz['id'], $taken_quizzes) ? 'bg-success text-white' : 'bg-light'; ?>">
                                                <h5 class="mb-0"><?php echo htmlspecialchars($quiz['titre']); ?></h5>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text"><?php echo htmlspecialchars($quiz['description']); ?></p>
                                                <p><strong>Durée:</strong> <?php echo isset($quiz['duree']) ? htmlspecialchars($quiz['duree']) . ' minutes' : 'Non spécifiée'; ?></p>
                                            </div>
                                            <div class="card-footer bg-white">
                                                <?php if (in_array($quiz['id'], $taken_quizzes)): ?>
                                                    <a href="index.php?page=evaluations&action=result&id=<?php echo $quiz['id']; ?>" class="btn btn-success w-100">
                                                        <i class="fas fa-check-circle me-2"></i>Voir mon résultat
                                                    </a>
                                                <?php else: ?>
                                                    <a href="index.php?page=evaluations&action=take&id=<?php echo $quiz['id']; ?>" class="btn btn-primary w-100">
                                                        <i class="fas fa-play-circle me-2"></i>Commencer le quiz
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (!isset($_GET['module_id'])): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Veuillez sélectionner un module pour voir les quizzes disponibles.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for module search functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('moduleSearch');
    const clearButton = document.getElementById('clearSearch');
    const moduleCards = document.querySelectorAll('.module-card');
    const noModulesFound = document.getElementById('noModulesFound');
    
    // Function to filter modules
    function filterModules() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let visibleCount = 0;
        
        moduleCards.forEach(card => {
            const moduleName = card.dataset.moduleName;
            if (moduleName.includes(searchTerm)) {
                card.classList.remove('d-none');
                visibleCount++;
            } else {
                card.classList.add('d-none');
            }
        });
        
        // Show/hide no results message
        if (visibleCount === 0) {
            noModulesFound.classList.remove('d-none');
        } else {
            noModulesFound.classList.add('d-none');
        }
    }
    
    // Event listeners
    searchInput.addEventListener('input', filterModules);
    
    clearButton.addEventListener('click', function() {
        searchInput.value = '';
        filterModules();
    });
});
</script>