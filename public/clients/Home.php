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
        'price_per_km' => 200,
        'eta' => '2-3 min',
        'capacity' => '1 pers'
    ],
    'voiture' => [
        'name' => 'Voiture',
        'icon' => 'fa-car',
        'base_price' => 1000,
        'price_per_km' => 350,
        'eta' => '3-5 min',
        'capacity' => '4 pers'
    ],
    'berline' => [
        'name' => 'Berline',
        'icon' => 'fa-car-side',
        'base_price' => 1500,
        'price_per_km' => 500,
        'eta' => '5-7 min',
        'capacity' => '4 pers'
    ],
    'van' => [
        'name' => 'Van',
        'icon' => 'fa-truck',
        'base_price' => 2000,
        'price_per_km' => 650,
        'eta' => '7-10 min',
        'capacity' => '8 pers'
    ]
];

// Adresses fictives
$savedAddresses = [
    'Maison' => '123 Rue de la Paix, Dakar',
    'Travail' => '45 Avenue Lamine Guèye, Dakar',
    'Gym' => '78 Rue Mermoz, Dakar'
];
?>

<div class="taxi-home">

    <!-- En-tête avec profil -->
    <div class="header">
        <div class="user-greeting">
            <h2>Bonjour, <?= htmlspecialchars($name ?: 'Cher client') ?> 👋</h2>
            <p>Où allez-vous aujourd'hui ?</p>
        </div>
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
    </div>

    <!-- Barre de recherche d'adresse -->
    <div class="location-search">
        <div class="location-inputs">
            <div class="input-group pickup">
                <i class="fas fa-circle"></i>
                <input type="text" id="pickupLocation" placeholder="Votre position" value="Position actuelle" readonly>
            </div>
            <div class="input-group destination">
                <i class="fas fa-square"></i>
                <input type="text" id="destinationInput" placeholder="Où allez-vous ?" autocomplete="off">
            </div>
        </div>
        <button class="swap-btn" id="swapLocations">
            <i class="fas fa-exchange-alt"></i>
        </button>
    </div>

    <!-- Adresses enregistrées -->
    <div class="saved-addresses" id="savedAddresses">
        <?php foreach ($savedAddresses as $label => $address): ?>
        <div class="address-chip" data-address="<?= htmlspecialchars($address) ?>">
            <i class="fas fa-map-marker-alt"></i>
            <span><?= $label ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Suggestions de destinations (cachées par défaut) -->
    <div class="suggestions" id="suggestions"></div>

    <!-- Carte -->
    <div class="map-container">
        <div id="map"></div>
        <div class="map-overlay">
            <button class="locate-me" id="locateMe">
                <i class="fas fa-location-arrow"></i>
            </button>
        </div>
    </div>

    <!-- Sélection des véhicules -->
    <div class="vehicle-section">
        <div class="section-header">
            <h3>Choisissez votre véhicule</h3>
            <span class="eta-label">Arrivée</span>
        </div>
        <div class="vehicle-list" id="vehicleList">
            <?php foreach ($vehicles as $key => $vehicle): ?>
            <div class="vehicle-card" data-vehicle="<?= $key ?>" data-base-price="<?= $vehicle['base_price'] ?>" data-price-km="<?= $vehicle['price_per_km'] ?>">
                <div class="vehicle-left">
                    <i class="fas <?= $vehicle['icon'] ?> vehicle-icon"></i>
                    <div class="vehicle-info">
                        <h4><?= $vehicle['name'] ?></h4>
                        <span class="vehicle-capacity"><i class="fas fa-user"></i> <?= $vehicle['capacity'] ?></span>
                    </div>
                </div>
                <div class="vehicle-right">
                    <span class="vehicle-eta"><?= $vehicle['eta'] ?></span>
                    <span class="vehicle-price" id="price-<?= $key ?>"><?= number_format($vehicle['base_price'], 0) ?> F</span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Estimation détaillée (apparaît après sélection) -->
    <div class="trip-estimate" id="tripEstimate" style="display: none;">
        <div class="estimate-header">
            <h3>Estimation de la course</h3>
            <span class="final-price" id="finalPrice">0 F</span>
        </div>
        <div class="estimate-details">
            <div class="detail-item">
                <span>Distance</span>
                <span id="distanceEstimate">0 km</span>
            </div>
            <div class="detail-item">
                <span>Durée</span>
                <span id="durationEstimate">0 min</span>
            </div>
            <div class="detail-item">
                <span>Prix de base</span>
                <span id="basePriceEstimate">0 F</span>
            </div>
            <div class="detail-item total">
                <span>Total</span>
                <span id="totalEstimate">0 F</span>
            </div>
        </div>
    </div>

    <!-- Bouton de commande -->
    <div class="order-section">
        <button class="order-btn" id="orderBtn" disabled>
            <span>Confirmer la course</span>
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>

</div>

<!-- Inclusion de la map (Leaflet) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// Données fictives pour les suggestions
const suggestionsData = [
    "Aéroport International Blaise Diagne",
    "Gare Routière de Pompiers",
    "Université Cheikh Anta Diop",
    "Plage de Ngor",
    "Marché Kermel",
    "Almadies"
];

// État de l'application
let map;
let pickupMarker, destinationMarker;
let pickupCoords = [14.7167, -17.4677]; // Dakar par défaut
let destinationCoords = null;
let selectedVehicle = 'moto';
let routeLine = null;
let distance = 0;
let duration = 0;

// Initialisation de la carte
function initMap() {
    map = L.map('map').setView(pickupCoords, 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marqueur de départ (position actuelle)
    pickupMarker = L.marker(pickupCoords, {
        icon: L.divIcon({
            className: 'pickup-marker',
            html: '<i class="fas fa-circle" style="color: #4CAF50; font-size: 20px;"></i>',
            iconSize: [20, 20]
        })
    }).addTo(map).bindPopup('Point de départ');

    // Gestionnaire de clic sur la carte
    map.on('click', function(e) {
        setDestination(e.latlng);
    });

    // Géolocalisation
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            pickupCoords = [position.coords.latitude, position.coords.longitude];
            map.setView(pickupCoords, 14);
            updatePickupMarker();
        });
    }
}

// Recherche de lieux (simulée)
function searchLocations(query) {
    const suggestions = document.getElementById('suggestions');
    suggestions.innerHTML = '';
    
    if (query.length < 2) {
        suggestions.style.display = 'none';
        return;
    }

    const filtered = suggestionsData.filter(item => 
        item.toLowerCase().includes(query.toLowerCase())
    );

    if (filtered.length > 0) {
        suggestions.style.display = 'block';
        filtered.forEach(item => {
            const div = document.createElement('div');
            div.className = 'suggestion-item';
            div.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${item}`;
            div.onclick = () => {
                document.getElementById('destinationInput').value = item;
                suggestions.style.display = 'none';
                // Simuler des coordonnées pour la démo
                setDestination([pickupCoords[0] + 0.01, pickupCoords[1] + 0.01]);
            };
            suggestions.appendChild(div);
        });
    } else {
        suggestions.style.display = 'none';
    }
}

// Définir la destination
function setDestination(coords) {
    if (destinationMarker) {
        map.removeLayer(destinationMarker);
    }

    destinationCoords = coords;
    
    destinationMarker = L.marker(coords, {
        icon: L.divIcon({
            className: 'destination-marker',
            html: '<i class="fas fa-map-pin" style="color: #ff6f61; font-size: 30px;"></i>',
            iconSize: [30, 30]
        })
    }).addTo(map).bindPopup('Destination');

    map.fitBounds([pickupCoords, coords], { padding: [50, 50] });
    
    // Simuler le calcul de distance
    distance = (Math.random() * 10 + 2).toFixed(1);
    duration = Math.round(distance * 3);
    
    drawRoute();
    updatePrices();
    
    document.getElementById('orderBtn').disabled = false;
}

// Dessiner un itinéraire simulé
function drawRoute() {
    if (routeLine) {
        map.removeLayer(routeLine);
    }

    // Points de contrôle pour simuler une route réaliste
    const latlngs = [
        pickupCoords,
        [pickupCoords[0] + (destinationCoords[0] - pickupCoords[0]) * 0.3, 
         pickupCoords[1] + (destinationCoords[1] - pickupCoords[1]) * 0.3 + 0.005],
        [pickupCoords[0] + (destinationCoords[0] - pickupCoords[0]) * 0.7, 
         pickupCoords[1] + (destinationCoords[1] - pickupCoords[1]) * 0.7 - 0.005],
        destinationCoords
    ];

    routeLine = L.polyline(latlngs, {
        color: '#ff6f61',
        weight: 4,
        opacity: 0.8,
        lineJoin: 'round'
    }).addTo(map);
}

// Mettre à jour les prix
function updatePrices() {
    const selectedCard = document.querySelector(`.vehicle-card[data-vehicle="${selectedVehicle}"]`);
    if (!selectedCard) return;

    const basePrice = parseInt(selectedCard.dataset.basePrice);
    const pricePerKm = parseInt(selectedCard.dataset.priceKm);
    const totalPrice = basePrice + (pricePerKm * distance);

    document.getElementById(`price-${selectedVehicle}`).innerHTML = totalPrice.toFixed(0) + ' F';
    
    // Mettre à jour l'estimation
    document.getElementById('tripEstimate').style.display = 'block';
    document.getElementById('finalPrice').innerHTML = totalPrice.toFixed(0) + ' F';
    document.getElementById('distanceEstimate').innerHTML = distance + ' km';
    document.getElementById('durationEstimate').innerHTML = duration + ' min';
    document.getElementById('basePriceEstimate').innerHTML = basePrice + ' F';
    document.getElementById('totalEstimate').innerHTML = totalPrice.toFixed(0) + ' F';
}

// Mettre à jour le marqueur de départ
function updatePickupMarker() {
    if (pickupMarker) {
        map.removeLayer(pickupMarker);
    }
    
    pickupMarker = L.marker(pickupCoords, {
        icon: L.divIcon({
            className: 'pickup-marker',
            html: '<i class="fas fa-circle" style="color: #4CAF50; font-size: 20px;"></i>',
            iconSize: [20, 20]
        })
    }).addTo(map);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    initMap();

    // Recherche de destinations
    document.getElementById('destinationInput').addEventListener('input', function(e) {
        searchLocations(e.target.value);
    });

    // Adresses enregistrées
    document.querySelectorAll('.address-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            document.getElementById('destinationInput').value = this.dataset.address;
            // Simuler des coordonnées
            setDestination([pickupCoords[0] + 0.015, pickupCoords[1] + 0.01]);
        });
    });

    // Sélection de véhicule
    document.querySelectorAll('.vehicle-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.vehicle-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedVehicle = this.dataset.vehicle;
            if (destinationCoords) {
                updatePrices();
            }
        });
    });

    // Bouton de localisation
    document.getElementById('locateMe').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                pickupCoords = [position.coords.latitude, position.coords.longitude];
                map.setView(pickupCoords, 15);
                updatePickupMarker();
            });
        }
    });

    // Bouton de commande
    document.getElementById('orderBtn').addEventListener('click', function() {
        if (!destinationCoords) return;
        
        const vehicle = document.querySelector(`.vehicle-card[data-vehicle="${selectedVehicle}"] h4`).innerHTML;
        const total = document.getElementById('totalEstimate').innerHTML;
        
        alert(`Course confirmée !\nVéhicule: ${vehicle}\nDistance: ${distance} km\nPrix total: ${total}\nUn chauffeur arrive dans quelques minutes.`);
    });
});
</script>