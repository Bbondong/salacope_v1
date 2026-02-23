<nav class="navbar">
    <ul class="nav-menu">
        <li>
            <a href="?page=Home" class="nav-item <?= $page=='Home'?'active':'' ?>">
                <i class="fas fa-home"></i>
                <span>Accueil</span>
            </a>
        </li>

        <li>
            <a href="?page=Trips" class="nav-item <?= $page=='Trips'?'active':'' ?>">
                <i class="fas fa-history"></i>
                <span>Courses</span>
            </a>
        </li>

        <li>
            <a href="?page=Favory" class="nav-item <?= $page=='Favory'?'active':'' ?>">
                <i class="fas fa-heart"></i>
                <span>Favoris</span>
            </a>
        </li>

        <li>
            <a href="?page=Payment" class="nav-item <?= $page=='Payment'?'active':'' ?>">
                <i class="fas fa-credit-card"></i>
                <span>Paiement</span>
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