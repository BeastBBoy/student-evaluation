<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Tableau de bord Administrateur</h4>
                </div>
                <div class="card-body">
                    <!-- Statistiques principales avec animations -->
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white h-100 shadow-sm hover-card">
                                <div class="card-body">
                                    <h5 class="card-title">Utilisateurs</h5>
                                    <div class="mt-3">
                                        <?php foreach ($stats['users_by_role'] as $role => $count): ?>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span><?php echo ucfirst($role); ?></span>
                                                <div class="progress flex-grow-1 mx-2" style="height: 10px;">
                                                    <div class="progress-bar bg-light" role="progressbar" style="width: <?php echo min(100, $count * 5); ?>%" aria-valuenow="<?php echo $count; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="badge bg-light text-dark"><?php echo $count; ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="index.php?page=admin&action=users" class="btn btn-light btn-sm w-100">
                                        <i class="fas fa-users me-2"></i>Gérer les utilisateurs
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white h-100 shadow-sm hover-card">
                                <div class="card-body">
                                    <h5 class="card-title">Modules</h5>
                                    <div class="d-flex align-items-center justify-content-center h-75">
                                        <div class="text-center">
                                            <p class="display-4 mb-0 counter" data-target="<?php echo $stats['modules_count']; ?>">0</p>
                                            <p class="mb-0">Total des modules</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="index.php?page=modules&action=index" class="btn btn-light btn-sm w-100">
                                        <i class="fas fa-book me-2"></i>Voir les modules
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card bg-warning text-dark h-100 shadow-sm hover-card">
                                <div class="card-body">
                                    <h5 class="card-title">Évaluations</h5>
                                    <div class="d-flex align-items-center justify-content-center h-75">
                                        <div class="text-center">
                                            <p class="display-4 mb-0 counter" data-target="<?php echo $stats['evaluations_count']; ?>">0</p>
                                            <p class="mb-0">Total des évaluations</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="index.php?page=evaluations&action=listQuizzes" class="btn btn-light btn-sm w-100">
                                        <i class="fas fa-file-alt me-2"></i>Voir les évaluations
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphiques et statistiques avancées -->
                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Quizzes par module</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="quizzesByModuleChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Scores moyens par module</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="avgScoresByModuleChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Activité récente (7 derniers jours)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="recentActivityChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions rapides avec icônes améliorées -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Actions rapides</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="index.php?page=admin&action=users" class="btn btn-outline-primary w-100 p-3 d-flex flex-column align-items-center action-btn">
                                                <i class="fas fa-users fa-2x mb-2"></i>
                                                <span>Gérer les utilisateurs</span>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="index.php?page=admin&action=modules" class="btn btn-outline-success w-100 p-3 d-flex flex-column align-items-center action-btn">
                                                <i class="fas fa-book fa-2x mb-2"></i>
                                                <span>Gérer les modules</span>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="index.php?page=admin&action=evaluations" class="btn btn-outline-info w-100 p-3 d-flex flex-column align-items-center action-btn">
                                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                                <span>Gérer les évaluations</span>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="index.php?page=evaluations&action=results" class="btn btn-outline-warning w-100 p-3 d-flex flex-column align-items-center action-btn">
                                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                                <span>Voir tous les résultats</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="index.php?page=auth&action=register" class="btn btn-outline-secondary w-100 p-3 d-flex flex-column align-items-center action-btn">
                                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                                <span>Ajouter un utilisateur</span>
                                            </a>
                                        </div>
                                        <div class="col-md-3 col-sm-6 mb-3">
                                            <a href="index.php?page=modules&action=create" class="btn btn-outline-dark w-100 p-3 d-flex flex-column align-items-center action-btn">
                                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                                <span>Créer un module</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Résultats récents avec design amélioré -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Résultats récents des quizzes</h5>
                                    <a href="index.php?page=evaluations&action=results" class="btn btn-sm btn-outline-primary">Voir tous</a>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recent_results)): ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>Aucun résultat récent disponible.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Étudiant</th>
                                                        <th>Évaluation</th>
                                                        <th>Module</th>
                                                        <th>Score</th>
                                                        <th>Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_results as $result): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($result['etudiant_nom']); ?></td>
                                                            <td><?php echo htmlspecialchars($result['evaluation_titre']); ?></td>
                                                            <td><?php echo htmlspecialchars($result['module_nom']); ?></td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                                        <div class="progress-bar <?php echo $result['score'] >= 50 ? 'bg-success' : 'bg-danger'; ?>" role="progressbar" style="width: <?php echo $result['score']; ?>%" aria-valuenow="<?php echo $result['score']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                    </div>
                                                                    <span><?php echo $result['score']; ?>%</span>
                                                                </div>
                                                            </td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($result['date_soumission'])); ?></td>
                                                            <td>
                                                                <a href="index.php?page=evaluations&action=result&id=<?php echo $result['id']; ?>" class="btn btn-sm btn-outline-info">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
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
            </div>
        </div>
    </div>
</div>

<!-- Ajout de CSS pour les animations et effets -->
<style>
.hover-card {
    transition: transform 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
}
.action-btn {
    transition: all 0.3s ease;
}
.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<!-- Ajout de JavaScript pour les animations et graphiques -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const duration = 1500; // ms
        const step = Math.ceil(target / (duration / 30)); // Update every 30ms
        
        let current = 0;
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = current;
            }
        }, 30);
    });
    
    // Graphique des quizzes par module
    const quizzesByModuleCtx = document.getElementById('quizzesByModuleChart').getContext('2d');
    const quizzesByModuleData = {
        labels: [<?php echo "'" . implode("', '", array_keys($stats['quizzes_by_module'])) . "'"; ?>],
        datasets: [{
            label: 'Nombre de quizzes',
            data: [<?php echo implode(", ", array_values($stats['quizzes_by_module'])); ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    };
    new Chart(quizzesByModuleCtx, {
        type: 'bar',
        data: quizzesByModuleData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // Graphique des scores moyens par module
    const avgScoresCtx = document.getElementById('avgScoresByModuleChart').getContext('2d');
    const avgScoresData = {
        labels: [<?php echo "'" . implode("', '", array_keys($stats['avg_scores_by_module'])) . "'"; ?>],
        datasets: [{
            label: 'Score moyen (%)',
            data: [<?php echo implode(", ", array_values($stats['avg_scores_by_module'])); ?>],
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1
        }]
    };
    new Chart(avgScoresCtx, {
        type: 'horizontalBar',
        data: avgScoresData,
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
    
    // Graphique d'activité récente
    const activityCtx = document.getElementById('recentActivityChart').getContext('2d');
    const activityData = {
        labels: [<?php echo "'" . implode("', '", array_keys($stats['recent_activity'])) . "'"; ?>],
        datasets: [{
            label: 'Quizzes complétés',
            data: [<?php echo implode(", ", array_values($stats['recent_activity'])); ?>],
            fill: true,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            tension: 0.4
        }]
    };
    new Chart(activityCtx, {
        type: 'line',
        data: activityData,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>