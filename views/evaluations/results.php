<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Résultats des évaluations</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($resultats)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun résultat disponible pour le moment.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Étudiant</th>
                                        <th>Évaluation</th>
                                        <th>Module</th>
                                        <th>Score</th>
                                        <th>Date de soumission</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($resultats as $resultat): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($resultat['etudiant_nom']); ?></td>
                                            <td><?php echo htmlspecialchars($resultat['evaluation_titre']); ?></td>
                                            <td><?php echo htmlspecialchars($resultat['module_nom']); ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar <?php echo $resultat['score'] >= 70 ? 'bg-success' : ($resultat['score'] >= 50 ? 'bg-warning' : 'bg-danger'); ?>" 
                                                         role="progressbar" 
                                                         style="width: <?php echo $resultat['score']; ?>%;" 
                                                         aria-valuenow="<?php echo $resultat['score']; ?>" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        <?php echo round($resultat['score'], 1); ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($resultat['date_soumission'])); ?></td>
                                            <td>
                                                <a href="index.php?page=evaluations&action=result&id=<?php echo $resultat['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i>Détails
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