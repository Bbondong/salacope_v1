<nav class="navbar-mobile">
    <ul class="nav-menu">
        <li>
            <a href="?page=Home" class="nav-item <?php echo $page == 'Home' ? 'active' : ''; ?>">
                <div class="nav-icon">
                    <i class="fas fa-home"></i>
                </div>
                <span class="nav-text">Accueil</span>
                <span class="active-indicator"></span>
            </a>
        </li>
        <li>
            <a href="?page=Category" class="nav-item <?php echo $page == 'Category' ? 'active' : ''; ?>">
                <div class="nav-icon">
                    <i class="fas fa-th-large"></i>
                </div>
                <span class="nav-text">Catégories</span>
                <span class="active-indicator"></span>
            </a>
        </li>
        <li>
            <a href="?page=Favory" class="nav-item <?php echo $page == 'Favory' ? 'active' : ''; ?>">
                <div class="nav-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <span class="nav-text">Favoris</span>
                <span class="active-indicator"></span>
            </a>
        </li>
        <li>
            <a href="?page=Chat" class="nav-item <?php echo $page == 'Chat' ? 'active' : ''; ?>">
                <div class="nav-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <span class="nav-text">Messages</span>
                <span class="active-indicator"></span>
            </a>
        </li>
        <li>
            <a href="?page=Profil" class="nav-item <?php echo $page == 'Profil' ? 'active' : ''; ?>">
                <div class="nav-icon">
                    <i class="fas fa-user"></i>
                </div>
                <span class="nav-text">Profil</span>
                <span class="active-indicator"></span>
            </a>
        </li>
    </ul>
</nav>