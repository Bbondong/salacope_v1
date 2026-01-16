<div class="sidebar">
    <div class="logo-container">
        <div class="logo">
            <i class="fas fa-store"></i>
            <span>Salacopp Admin</span>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <a href="?page=dashboard" class="menu-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="?page=products" class="menu-item <?php echo $page == 'products' ? 'active' : ''; ?>">
            <i class="fas fa-box-open"></i>
            <span>Produits</span>
        </a>
        <a href="?page=publish" class="menu-item <?php echo $page == 'publish' ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i>
            <span>Publier un produit</span>
        </a>
        <a href="?page=chat" class="menu-item <?php echo $page == 'chat' ? 'active' : ''; ?>">
            <i class="fas fa-comments"></i>
            <span>Messagerie</span>
            <span class="notification-badge" style="margin-left: auto;">5</span>
        </a>
        <a href="?page=subscriptions" class="menu-item <?php echo $page == 'subscriptions' ? 'active' : ''; ?>">
            <i class="fas fa-id-card"></i>
            <span>Abonnements</span>
        </a>
        <a href="?page=settings" class="menu-item <?php echo $page == 'settings' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            <span>Paramètres</span>
        </a>
        <a href="logout.php" class="logout-btn menu-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Paramètres</span>
        </a>
    </div>
    
    <div class="admin-profile">
        <div class="profile-img">
            <?php echo strtoupper(substr($adminName, 0, 1)); ?>
        </div>
        <div class="profile-info">
            <h4><?php echo $adminName; ?></h4>
            <p>Administrateur</p>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
        </a>
    </div>
</div>
