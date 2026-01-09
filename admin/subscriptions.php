<h2>Gestion des abonnements</h2>
<p class="page-description">Suivez les abonnements actifs et leurs statistiques.</p>

<div class="subscriptions-table">
    <div class="table-header">
        <h3>Liste des abonnements</h3>
        <div class="table-actions">
            <button class="btn btn-primary">
                <i class="fas fa-download"></i>
                Exporter
            </button>
        </div>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Type</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Prix</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#SUB001</td>
                    <td>Jean Dupont</td>
                    <td>Premium</td>
                    <td>15/06/2023</td>
                    <td>15/07/2023</td>
                    <td>$29.99</td>
                    <td><span class="badge badge-active">Actif</span></td>
                    <td>
                        <button class="btn-action">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>#SUB002</td>
                    <td>Marie Martin</td>
                    <td>Basique</td>
                    <td>10/06/2023</td>
                    <td>10/07/2023</td>
                    <td>$9.99</td>
                    <td><span class="badge badge-active">Actif</span></td>
                    <td>
                        <button class="btn-action">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>#SUB003</td>
                    <td>Pierre Lambert</td>
                    <td>Premium</td>
                    <td>01/06/2023</td>
                    <td>01/07/2023</td>
                    <td>$29.99</td>
                    <td><span class="badge badge-expired">Expiré</span></td>
                    <td>
                        <button class="btn-action">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>#SUB004</td>
                    <td>Sophie Chartier</td>
                    <td>Entreprise</td>
                    <td>20/06/2023</td>
                    <td>20/09/2023</td>
                    <td>$99.99</td>
                    <td><span class="badge badge-active">Actif</span></td>
                    <td>
                        <button class="btn-action">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="stats-container" style="margin-top: 30px;">
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #3498db;">
            <i class="fas fa-user-check"></i>
        </div>
        <div class="stat-info">
            <h3>342</h3>
            <p>Abonnements actifs</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #2ecc71;">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-info">
            <h3>$8,245</h3>
            <p>Revenus mensuels</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background-color: #9b59b6;">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-info">
            <h3>+12%</h3>
            <p>Croissance ce mois</p>
        </div>
    </div>
</div>