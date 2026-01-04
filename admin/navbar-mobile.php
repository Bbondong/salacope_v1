<?php
// admin/navbar-mobile.php
$current_page = $_GET['page'] ?? 'dashboard';
?>

<!-- Mobile Bottom Navbar -->
<nav class="navbar-mobile">
    <a href="index.php?page=dashboard" class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
        <i class="fas fa-home"></i>
        <span>Accueil</span>
    </a>
    
    <a href="index.php?page=publications" class="nav-item <?php echo $current_page === 'publications' ? 'active' : ''; ?>">
        <i class="fas fa-bullhorn"></i>
        <span>Publications</span>
    </a>
    
    <a href="index.php?page=articles" class="nav-item <?php echo $current_page === 'articles' ? 'active' : ''; ?>">
        <i class="fas fa-newspaper"></i>
        <span>Articles</span>
    </a>
    
    <a href="index.php?page=profile" class="nav-item <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
        <i class="fas fa-user"></i>
        <span>Profil</span>
    </a>
    
    <div class="nav-item menu-toggle">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
    </div>
    
    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay">
        <div class="mobile-menu-content">
            <div class="mobile-menu-header">
                <div class="mobile-user">
                    <img src="<?php echo htmlspecialchars($admin_avatar); ?>" alt="Admin">
                    <div>
                        <h3><?php echo htmlspecialchars($admin_name); ?></h3>
                        <span><?php echo htmlspecialchars($admin_role); ?></span>
                    </div>
                </div>
                <button class="mobile-menu-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mobile-menu-items">
                <div class="menu-section">
                    <h4>TABLEAU DE BORD</h4>
                    <a href="index.php?page=dashboard" class="mobile-menu-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Accueil</span>
                    </a>
                    <a href="index.php?page=reports" class="mobile-menu-item <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                        <i class="fas fa-chart-bar"></i>
                        <span>Rapports</span>
                    </a>
                </div>
                
                <div class="menu-section">
                    <h4>GESTION</h4>
                    <a href="index.php?page=users" class="mobile-menu-item <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        <span>Utilisateurs</span>
                        <span class="badge">245</span>
                    </a>
                    <a href="index.php?page=sellers" class="mobile-menu-item <?php echo $current_page === 'sellers' ? 'active' : ''; ?>">
                        <i class="fas fa-store"></i>
                        <span>Vendeurs</span>
                        <span class="badge">89</span>
                    </a>
                    <a href="index.php?page=buyers" class="mobile-menu-item <?php echo $current_page === 'buyers' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Acheteurs</span>
                        <span class="badge">156</span>
                    </a>
                </div>
                
                <div class="menu-section">
                    <h4>CONTENU</h4>
                    <a href="index.php?page=publications" class="mobile-menu-item <?php echo $current_page === 'publications' ? 'active' : ''; ?>">
                        <i class="fas fa-bullhorn"></i>
                        <span>Publications</span>
                    </a>
                    <a href="index.php?page=articles" class="mobile-menu-item <?php echo $current_page === 'articles' ? 'active' : ''; ?>">
                        <i class="fas fa-newspaper"></i>
                        <span>Articles</span>
                    </a>
                    <a href="index.php?page=ads" class="mobile-menu-item <?php echo $current_page === 'ads' ? 'active' : ''; ?>">
                        <i class="fas fa-ad"></i>
                        <span>Publicités</span>
                    </a>
                </div>
                
                <div class="menu-section">
                    <h4>PARAMÈTRES</h4>
                    <a href="index.php?page=settings" class="mobile-menu-item <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                        <i class="fas fa-cog"></i>
                        <span>Paramètres</span>
                    </a>
                    <a href="index.php?page=support" class="mobile-menu-item <?php echo $current_page === 'support' ? 'active' : ''; ?>">
                        <i class="fas fa-headset"></i>
                        <span>Support</span>
                    </a>
                    <a href="logout.php" class="mobile-menu-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Quick Action Button Mobile -->
<button class="quick-action-btn">
    <i class="fas fa-plus"></i>
</button>