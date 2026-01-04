<?php
// admin/header.php
?>

<!-- Navbar Desktop -->
<nav class="navbar-desktop">
    <div class="navbar-brand">
        <div class="logo">
            <i class="fas fa-fire"></i>
            <span>SALACOPE</span>
        </div>
        <div class="admin-badge">
            <i class="fas fa-crown"></i>
            <span>Admin Principal</span>
        </div>
    </div>
    
    <div class="navbar-search">
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Rechercher un vendeur, client, publication...">
        </div>
    </div>
    
    <div class="navbar-user">
        <div class="notifications">
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-count">3</span>
            </button>
            <div class="notification-dropdown">
                <div class="notification-header">
                    <h4>Notifications</h4>
                    <span class="mark-read">Tout marquer comme lu</span>
                </div>
                <div class="notification-list">
                    <div class="notification-item new">
                        <div class="notification-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="notification-content">
                            <p>Nouvel achat effectué par <strong>Jean Dupont</strong></p>
                            <span class="notification-time">2 min</span>
                        </div>
                    </div>
                    <div class="notification-item new">
                        <div class="notification-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="notification-content">
                            <p>Nouveau vendeur inscrit: <strong>ShopTech</strong></p>
                            <span class="notification-time">15 min</span>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="notification-content">
                            <p>Signalement de publication #245</p>
                            <span class="notification-time">1 heure</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="user-menu">
            <div class="user-avatar">
                <img src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="Admin" id="adminAvatar">
                <span class="online-status"></span>
            </div>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($admin_name); ?></span>
                <span class="user-role"><?php echo htmlspecialchars($admin_role); ?></span>
            </div>
            <button class="menu-toggle">
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-dropdown">
                <a href="index.php?page=profile" class="dropdown-item">
                    <i class="fas fa-user"></i> Mon profil
                </a>
                <a href="index.php?page=settings" class="dropdown-item">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
                <div class="dropdown-divider"></div>
                <a href="index.php?page=articles" class="dropdown-item">
                    <i class="fas fa-newspaper"></i> Gérer les articles
                </a>
                <a href="index.php?page=publications" class="dropdown-item">
                    <i class="fas fa-bullhorn"></i> Publications
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item logout">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>
    </div>
</nav>