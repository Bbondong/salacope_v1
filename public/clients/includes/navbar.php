<nav class="navbar">
    <ul class="nav-menu">
        <li>
            <a href="?page=Home" class="nav-item <?= $page=='Home'?'active':'' ?>">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
        </li>

        <li>
            <a href="?page=Category" class="nav-item <?= $page=='Category'?'active':'' ?>">
                <i class="fas fa-th-large"></i>
                <span>Catégories</span>
            </a>
        </li>

        <li>
            <a href="?page=Favory" class="nav-item <?= $page=='Favory'?'active':'' ?>">
                <i class="fas fa-heart"></i>
                <span>Favoris</span>
            </a>
        </li>

        <li>
            <a href="?page=Chat" class="nav-item <?= $page=='Chat'?'active':'' ?>">
                <i class="fas fa-comments"></i>
                <span>Messages</span>
            </a>
        </li>

        <li>
            <a href="?page=Profil" class="nav-item <?= $page=='Profil'?'active':'' ?>">
                <i class="fas fa-user"></i>
                <span>Profil</span>
            </a>
        </li>
    </ul>
</nav>
