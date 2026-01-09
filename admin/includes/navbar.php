<nav class="navbar-mobile">
    <ul class="nav-menu">
        <li>
            <a href="?page=dashboard" class="nav-item <?php echo $page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="?page=products" class="nav-item <?php echo $page == 'products' ? 'active' : ''; ?>">
                <i class="fas fa-box-open"></i>
                <span>Produits</span>
            </a>
        </li>
        <li>
            <a href="?page=publish" class="nav-item <?php echo $page == 'publish' ? 'active' : ''; ?>">
                <i class="fas fa-plus"></i>
                <span>Publier</span>
            </a>
        </li>
        <li>
            <a href="?page=chat" class="nav-item <?php echo $page == 'chat' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i>
                <span>Chat</span>
            </a>
        </li>
        <li>
            <a href="?page=subscriptions" class="nav-item <?php echo $page == 'subscriptions' ? 'active' : ''; ?>">
                <i class="fas fa-id-card"></i>
                <span>Abonnements</span>
            </a>
        </li>
    </ul>
</nav>