<?php
// admin/sidebar.php
$current_page = $_GET['page'] ?? 'dashboard';
?>

<!-- Sidebar Desktop -->
<aside class="sidebar-desktop">
    <div class="sidebar-header">
        <h3><i class="fas fa-tachometer-alt"></i> Tableau de bord</h3>
    </div>
    
    <nav class="sidebar-menu">
        <a href="index.php?page=dashboard" class="menu-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Accueil</span>
        </a>
        
        <div class="menu-section">
            <h4>GESTION</h4>
            <a href="index.php?page=users" class="menu-item <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Utilisateurs</span>
                <span class="menu-badge">245</span>
            </a>
            <a href="index.php?page=sellers" class="menu-item <?php echo $current_page === 'sellers' ? 'active' : ''; ?>">
                <i class="fas fa-store"></i>
                <span>Vendeurs</span>
                <span class="menu-badge">89</span>
            </a>
            <a href="index.php?page=buyers" class="menu-item <?php echo $current_page === 'buyers' ? 'active' : ''; ?>">
                <i class="fas fa-shopping-cart"></i>
                <span>Acheteurs</span>
                <span class="menu-badge">156</span>
            </a>
        </div>
        
        <div class="menu-section">
            <h4>CONTENU</h4>
            <a href="index.php?page=publications" class="menu-item <?php echo $current_page === 'publications' ? 'active' : ''; ?>">
                <i class="fas fa-bullhorn"></i>
                <span>Publications</span>
                <span class="menu-badge">1.2K</span>
            </a>
            <a href="index.php?page=articles" class="menu-item <?php echo $current_page === 'articles' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i>
                <span>Articles</span>
                <span class="menu-badge">45</span>
            </a>
            <a href="index.php?page=ads" class="menu-item <?php echo $current_page === 'ads' ? 'active' : ''; ?>">
                <i class="fas fa-ad"></i>
                <span>Publicités</span>
            </a>
        </div>
        
        <div class="menu-section">
            <h4>FINANCES</h4>
            <a href="index.php?page=subscriptions" class="menu-item <?php echo $current_page === 'subscriptions' ? 'active' : ''; ?>">
                <i class="fas fa-crown"></i>
                <span>Abonnements</span>
            </a>
            <a href="index.php?page=transactions" class="menu-item <?php echo $current_page === 'transactions' ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i>
                <span>Transactions</span>
            </a>
            <a href="index.php?page=reports" class="menu-item <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Rapports</span>
            </a>
        </div>
        
        <div class="menu-section">
            <h4>SYSTÈME</h4>
            <a href="index.php?page=settings" class="menu-item <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
            </a>
            <a href="index.php?page=support" class="menu-item <?php echo $current_page === 'support' ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i>
                <span>Support</span>
            </a>
            <a href="index.php?page=logs" class="menu-item <?php echo $current_page === 'logs' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>Journaux</span>
            </a>
        </div>
        
        <div class="sidebar-footer">
            <div class="storage-info">
                <div class="storage-label">
                    <span>Stockage</span>
                    <span>65%</span>
                </div>
                <div class="storage-bar">
                    <div class="storage-fill" style="width: 65%"></div>
                </div>
            </div>
            <button class="btn-upgrade">
                <i class="fas fa-rocket"></i> Mettre à niveau
            </button>
        </div>
    </nav>
</aside>