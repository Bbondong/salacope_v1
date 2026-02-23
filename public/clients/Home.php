<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}

// Données fictives pour les véhicules
$vehicles = [
    'moto' => [
        'name' => 'Moto',
        'icon' => 'fa-motorcycle',
        'base_price' => 500,
        'price_per_km' => 250,
        'eta' => '2 min',
        'capacity' => '1',
        'image' => 'https://cdn-icons-png.flaticon.com/512/3097/3097180.png'
    ],
    'voiture' => [
        'name' => 'Berline',
        'icon' => 'fa-car',
        'base_price' => 1000,
        'price_per_km' => 400,
        'eta' => '4 min',
        'capacity' => '4',
        'image' => 'https://cdn-icons-png.flaticon.com/512/3097/3097139.png'
    ],
    'suv' => [
        'name' => 'SUV',
        'icon' => 'fa-car-side',
        'base_price' => 1500,
        'price_per_km' => 550,
        'eta' => '6 min',
        'capacity' => '6',
        'image' => 'https://cdn-icons-png.flaticon.com/512/3097/3097143.png'
    ],
    'van' => [
        'name' => 'Van',
        'icon' => 'fa-truck',
        'base_price' => 2000,
        'price_per_km' => 700,
        'eta' => '8 min',
        'capacity' => '8',
        'image' => 'https://cdn-icons-png.flaticon.com/512/3097/3097149.png'
    ]
];

$currencySymbol = 'F';
?>

<div class="taxi-home-2026">

    <!-- Header minimaliste avec fond glass -->
    <div class="header-glass">
        <div class="greeting">
            <span class="wave">👋</span>
            <div>
                <p class="hello">Bonjour,</p>
                <h2><?= htmlspecialchars($name ?: 'Cher client') ?></h2>
            </div>
        </div>
        <div class="profile-ring" onclick="window.location.href='?page=Profil'">
            <i class="fas fa-user"></i>
        </div>
    </div>

    <!-- Section recherche destination avec design 2026 -->
    <div class="search-section">
        <div class="location-glass">
            <div class="location-item">
                <div class="icon-bg pickup">
                    <i class="fas fa-circle"></i>
                </div>
                <div class="location-details">
                    <span class="label">Départ</span>
                    <span class="value" id="pickupDisplay">Position actuelle</span>
                </div>
            </div>
            <div class="location-divider"></div>
            <div class="location-item">
                <div class="icon-bg destination">
                    <i class="fas fa-map-pin"></i>
                </div>
                <div class="location-details">
                    <span class="label">Destination</span>
                    <input type="text" id="destinationInput" placeholder="Où allez-vous ?" autocomplete="off">
                </div>
            </div>
        </div>
        <button class="swap-btn" id="swapLocations">
            <i class="fas fa-arrow-down-arrow-up"></i>
        </button>
    </div>

    <!-- Suggestions élégantes -->
    <div class="suggestions-panel" id="suggestions"></div>

    <!-- Carte avec overlay glass -->
    <div class="map-wrapper">
        <div id="map"></div>
        <div class="map-overlay-2026">
            <button class="map-btn" id="locateMe">
                <i class="fas fa-location-arrow"></i>
            </button>
            <button class="map-btn" id="zoomIn">
                <i class="fas fa-plus"></i>
            </button>
            <button class="map-btn" id="zoomOut">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    <!-- Adresses rapides en chips modernes -->
    <div class="quick-addresses">
        <div class="chip" data-address="Dakar, Sénégal">
            <i class="fas fa-home"></i>
            <span>Maison</span>
        </div>
        <div class="chip" data-address="Plateau, Dakar">
            <i class="fas fa-briefcase"></i>
            <span>Travail</span>
        </div>
        <div class="chip" data-address="Aéroport Blaise Diagne">
            <i class="fas fa-plane"></i>
            <span>Aéroport</span>
        </div>
    </div>

    <!-- Section véhicules en cartes luxueuses -->
    <div class="vehicles-section">
        <div class="section-header-2026">
            <h3>Choisissez votre véhicule</h3>
            <span class="badge">Disponibles</span>
        </div>

        <div class="vehicles-grid">
            <?php foreach ($vehicles as $key => $vehicle): 
                $priceInCurrency = $vehicle['base_price'];
            ?>
            <div class="vehicle-card-2026" data-vehicle="<?= $key ?>" 
                 data-base-price="<?= $vehicle['base_price'] ?>"
                 data-price-km="<?= $vehicle['price_per_km'] ?>">
                <div class="vehicle-image">
                    <img src="<?= $vehicle['image'] ?>" alt="<?= $vehicle['name'] ?>">
                </div>
                <div class="vehicle-content">
                    <div class="vehicle-header">
                        <h4><?= $vehicle['name'] ?></h4>
                        <span class="capacity"><i class="fas fa-user"></i> <?= $vehicle['capacity'] ?></span>
                    </div>
                    <div class="vehicle-footer">
                        <span class="eta"><i class="fas fa-clock"></i> <?= $vehicle['eta'] ?></span>
                        <span class="price" id="price-<?= $key ?>"><?= $priceInCurrency ?> <?= $currencySymbol ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Carte de résumé de course (apparaît après sélection) -->
    <div class="trip-summary-card" id="tripSummary" style="display: none;">
        <div class="summary-header">
            <span class="title">Récapitulatif de votre course</span>
            <span class="price-tag" id="finalPrice">0 <?= $currencySymbol ?></span>
        </div>
        
        <div class="summary-route">
            <div class="route-point">
                <div class="dot green"></div>
                <span class="address" id="pickupAddress">Chargement...</span>
            </div>
            <div class="route-line"></div>
            <div class="route-point">
                <div class="dot red"></div>
                <span class="address" id="destinationAddress">Non définie</span>
            </div>
        </div>

        <div class="summary-details">
            <div class="detail-row">
                <span><i class="fas fa-route"></i> Distance</span>
                <span class="value" id="distanceEstimate">0 km</span>
            </div>
            <div class="detail-row">
                <span><i class="fas fa-clock"></i> Durée</span>
                <span class="value" id="durationEstimate">0 min</span>
            </div>
            <div class="detail-row highlight">
                <span><i class="fas fa-coins"></i> Prix total</span>
                <span class="value" id="totalEstimate">0 <?= $currencySymbol ?></span>
            </div>
        </div>

        <button class="confirm-btn" id="orderBtn" disabled>
            <span>Confirmer la course</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </div>

</div>

<!-- Librairies -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// État de l'application
let map;
let pickupCoords = null;
let destCoords = null;
let selectedVehicle = 'voiture';
let pickupMarker, destMarker, routeLine;
let currentDistance = 0;
let currentDuration = 0;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initMap();
    initEventListeners();
});

function initMap() {
    map = L.map('map', {
        zoomControl: false,
        attributionControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Géolocalisation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                pickupCoords = [position.coords.latitude, position.coords.longitude];
                map.setView(pickupCoords, 14);
                
                pickupMarker = L.marker(pickupCoords, {
                    icon: L.divIcon({
                        className: 'custom-marker',
                        html: '<div class="marker-pulse"></div><i class="fas fa-circle" style="color: #4CAF50; font-size: 20px;"></i>',
                        iconSize: [20, 20]
                    })
                }).addTo(map);
                
                reverseGeocode(pickupCoords);
            },
            function() {
                pickupCoords = [14.7167, -17.4677];
                map.setView(pickupCoords, 12);
            }
        );
    }

    // Clic sur la carte pour destination
    map.on('click', function(e) {
        setDestination([e.latlng.lat, e.latlng.lng]);
    });
}

// Recherche de lieux
async function searchLocations(query) {
    if (query.length < 2) {
        document.getElementById('suggestions').style.display = 'none';
        return;
    }

    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5`);
        const data = await response.json();
        
        const suggestions = document.getElementById('suggestions');
        suggestions.innerHTML = '';
        
        if (data.length > 0) {
            suggestions.style.display = 'block';
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'suggestion-item-2026';
                div.innerHTML = `
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <strong>${item.display_name.split(',')[0]}</strong>
                        <small>${item.display_name.split(',').slice(1,3).join(',')}</small>
                    </div>
                `;
                div.onclick = () => {
                    document.getElementById('destinationInput').value = item.display_name.split(',')[0];
                    suggestions.style.display = 'none';
                    setDestination([parseFloat(item.lat), parseFloat(item.lon)]);
                };
                suggestions.appendChild(div);
            });
        }
    } catch (error) {
        console.error('Erreur:', error);
    }
}

// Définir destination
function setDestination(coords) {
    destCoords = coords;
    
    if (destMarker) map.removeLayer(destMarker);
    
    destMarker = L.marker(coords, {
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-map-pin" style="color: #ff6f61; font-size: 30px;"></i>',
            iconSize: [30, 30]
        })
    }).addTo(map);
    
    map.fitBounds([pickupCoords, coords], { padding: [50, 50] });
    calculateRoute(pickupCoords, coords);
    reverseGeocode(coords, 'destination');
    
    document.getElementById('orderBtn').disabled = false;
}

// Calcul itinéraire
async function calculateRoute(start, end) {
    const url = `https://router.project-osrm.org/route/v1/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full`;
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.routes && data.routes[0]) {
            const route = data.routes[0];
            currentDistance = route.distance / 1000;
            currentDuration = route.duration / 60;
            
            // Dessiner la route
            if (routeLine) map.removeLayer(routeLine);
            
            const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            routeLine = L.polyline(coordinates, {
                color: '#ff6f61',
                weight: 4,
                opacity: 0.8
            }).addTo(map);
            
            updatePrices();
        }
    } catch (error) {
        console.error('Erreur calcul:', error);
    }
}

// Mise à jour des prix
function updatePrices() {
    const vehicleCards = document.querySelectorAll('.vehicle-card-2026');
    
    vehicleCards.forEach(card => {
        const basePrice = parseInt(card.dataset.basePrice);
        const pricePerKm = parseInt(card.dataset.priceKm);
        const total = basePrice + (pricePerKm * currentDistance);
        
        card.querySelector('.price').innerHTML = Math.round(total) + ' F';
        
        if (card.dataset.vehicle === selectedVehicle) {
            document.getElementById('tripSummary').style.display = 'block';
            document.getElementById('finalPrice').innerHTML = Math.round(total) + ' F';
            document.getElementById('distanceEstimate').innerHTML = currentDistance.toFixed(1) + ' km';
            document.getElementById('durationEstimate').innerHTML = Math.round(currentDuration) + ' min';
            document.getElementById('totalEstimate').innerHTML = Math.round(total) + ' F';
        }
    });
}

// Géocodage inverse
async function reverseGeocode(coords, type = 'pickup') {
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${coords[0]}&lon=${coords[1]}`);
        const data = await response.json();
        
        if (type === 'pickup') {
            document.getElementById('pickupDisplay').innerHTML = data.display_name?.split(',')[0] || 'Position actuelle';
            document.getElementById('pickupAddress').innerHTML = data.display_name || 'Position actuelle';
        } else {
            document.getElementById('destinationAddress').innerHTML = data.display_name || 'Destination';
        }
    } catch (error) {
        console.error('Erreur géocodage:', error);
    }
}

// Event listeners
function initEventListeners() {
    // Recherche
    let timeout;
    document.getElementById('destinationInput').addEventListener('input', function(e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => searchLocations(e.target.value), 300);
    });

    // Adresses rapides
    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.getElementById('destinationInput').value = this.querySelector('span').innerHTML;
            searchLocations(this.querySelector('span').innerHTML);
        });
    });

    // Sélection véhicule
    document.querySelectorAll('.vehicle-card-2026').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.vehicle-card-2026').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedVehicle = this.dataset.vehicle;
            if (currentDistance > 0) updatePrices();
        });
    });

    // Bouton localisation
    document.getElementById('locateMe').addEventListener('click', function() {
        if (pickupCoords) map.setView(pickupCoords, 16);
    });

    // Zoom
    document.getElementById('zoomIn').addEventListener('click', () => map.zoomIn());
    document.getElementById('zoomOut').addEventListener('click', () => map.zoomOut());

    // Commande
    document.getElementById('orderBtn').addEventListener('click', function() {
        const vehicle = document.querySelector(`.vehicle-card-2026[data-vehicle="${selectedVehicle}"] h4`).innerHTML;
        alert(`✅ Course confirmée !\nVéhicule: ${vehicle}\nPrix: ${document.getElementById('totalEstimate').innerHTML}`);
    });
}
</script>