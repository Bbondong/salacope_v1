<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: ../');
    exit;
}

// Données fictives pour les véhicules (prix de base en FCFA)
$vehicles = [
    'moto' => [
        'name' => 'Moto',
        'icon' => 'fa-motorcycle',
        'base_price' => 500,     // Prix de base en FCFA
        'price_per_km' => 250,    // Par km en FCFA
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
?>

<div class="taxi-home-2026">

    <!-- Header glass avec indicateur de pays -->
    <div class="header-glass">
        <div class="greeting">
            <span class="wave">👋</span>
            <div>
                <p class="hello">Bonjour,</p>
                <h2><?= htmlspecialchars($name ?: 'Cher client') ?></h2>
                <p class="location-status" id="currentLocationText">Détection en cours...</p>
            </div>
        </div>
        <div class="profile-ring" onclick="window.location.href='?page=Profil'">
            <i class="fas fa-user"></i>
        </div>
    </div>

    <!-- Section recherche -->
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

    <!-- Suggestions -->
    <div class="suggestions-panel" id="suggestions"></div>

    <!-- Carte avec trajet -->
    <div class="map-wrapper">
        <div id="map"></div>
        <div class="map-overlay-2026">
            <button class="map-btn" id="locateMe" title="Ma position">
                <i class="fas fa-location-arrow"></i>
            </button>
            <button class="map-btn" id="zoomIn" title="Zoom +">
                <i class="fas fa-plus"></i>
            </button>
            <button class="map-btn" id="zoomOut" title="Zoom -">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <!-- Légende du trajet -->
        <div class="route-legend" id="routeLegend" style="display: none;">
            <div class="legend-item">
                <span class="dot green"></span>
                <span id="legendPickup">Départ</span>
            </div>
            <div class="legend-item">
                <span class="dot red"></span>
                <span id="legendDestination">Destination</span>
            </div>
            <div class="legend-item">
                <i class="fas fa-road"></i>
                <span id="legendDistance">0 km</span>
            </div>
        </div>
    </div>

    <!-- Adresses rapides (dynamiques selon pays) -->
    <div class="quick-addresses" id="quickAddresses">
        <!-- Sera rempli par JS selon le pays -->
    </div>

    <!-- Véhicules -->
    <div class="vehicles-section">
        <div class="section-header-2026">
            <h3>Choisissez votre véhicule</h3>
            <span class="badge">Disponibles</span>
        </div>

        <div class="vehicles-grid">
            <?php foreach ($vehicles as $key => $vehicle): ?>
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
                        <span class="price" id="price-<?= $key ?>"><?= $vehicle['base_price'] ?> F</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Résumé course -->
    <div class="trip-summary-card" id="tripSummary" style="display: none;">
        <div class="summary-header">
            <span class="title">Récapitulatif de votre course</span>
            <span class="price-tag" id="finalPrice">0 F</span>
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
                <span class="value" id="totalEstimate">0 F</span>
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

<style>
/* Styles pour la légende du trajet */
.route-legend {
    position: absolute;
    top: 15px;
    left: 15px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    padding: 10px 15px;
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    z-index: 1000;
    display: flex;
    gap: 15px;
    font-size: 0.8rem;
    border: 1px solid rgba(255,111,97,0.2);
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #555;
}

.legend-item i {
    color: var(--primary);
    font-size: 0.8rem;
}

.legend-item .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.legend-item .dot.green {
    background: #4CAF50;
    box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.3);
}

.legend-item .dot.red {
    background: var(--primary);
    box-shadow: 0 0 0 2px rgba(255, 111, 97, 0.3);
}

.location-status {
    font-size: 0.7rem;
    color: #888;
    margin-top: 2px;
}

/* Animation de chargement */
@keyframes pulse {
    0% { transform: scale(0.5); opacity: 1; }
    100% { transform: scale(1.5); opacity: 0; }
}
.marker-pulse {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(76, 175, 80, 0.3);
    position: absolute;
    top: -10px;
    left: -10px;
    animation: pulse 2s infinite;
}
</style>

<!-- ============================================ -->
<!-- LE JS COMPLET AVEC DÉTECTION MONDIALE        -->
<!-- ============================================ -->
<script>
// ============================================
// TAXI APP 2026 - DÉTECTION MONDIALE + DEVISES
// ============================================

// Configuration globale
const CONFIG = {
    osrmServer: 'https://router.project-osrm.org/route/v1',
    nominatimServer: 'https://nominatim.openstreetmap.org',
    ipApiServer: 'https://ipapi.co/json',
    defaultZoom: 14
};

// État de l'application
let map;
let pickupCoords = null;
let destCoords = null;
let selectedVehicle = 'voiture';
let pickupMarker, destMarker, routeLine;
let currentDistance = 0;
let currentDuration = 0;
let userCountry = null;
let userCountryCode = null;
let userCurrency = 'FCFA';
let currencySymbol = 'F';
let exchangeRate = 1;

// ============================================
// 1. BASE DE DONNÉES DES DEVISES MONDIALES
// ============================================

const worldCurrencies = {
    // ======== AFRIQUE ========
    // Afrique de l'Ouest (UEMOA - Franc CFA)
    'BJ': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Bénin
    'BF': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Burkina Faso
    'CI': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Côte d'Ivoire
    'GW': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Guinée-Bissau
    'ML': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Mali
    'NE': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Niger
    'SN': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Sénégal
    'TG': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Togo
    
    // Afrique Centrale (CEMAC - Franc CFA)
    'CM': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Cameroun
    'CF': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Centrafrique
    'TD': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Tchad
    'CG': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Congo-Brazzaville
    'GQ': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Guinée Équatoriale
    'GA': { currency: 'FCFA', symbol: 'F', name: 'Franc CFA', rate: 1 }, // Gabon
    
    // RDC et autres
    'CD': { currency: 'CDF', symbol: 'FC', name: 'Franc Congolais', rate: 2850 }, // RDC
    'RW': { currency: 'RWF', symbol: 'Fr', name: 'Franc Rwandais', rate: 1300 }, // Rwanda
    'BI': { currency: 'BIF', symbol: 'Fr', name: 'Franc Burundais', rate: 2800 }, // Burundi
    'UG': { currency: 'UGX', symbol: 'USh', name: 'Shilling Ougandais', rate: 4500 }, // Ouganda
    'KE': { currency: 'KES', symbol: 'KSh', name: 'Shilling Kenyan', rate: 180 }, // Kenya
    'TZ': { currency: 'TZS', symbol: 'TSh', name: 'Shilling Tanzanien', rate: 3000 }, // Tanzanie
    'ET': { currency: 'ETB', symbol: 'Br', name: 'Birr Éthiopien', rate: 70 }, // Éthiopie
    'NG': { currency: 'NGN', symbol: '₦', name: 'Naira', rate: 1500 }, // Nigeria
    'GH': { currency: 'GHS', symbol: '₵', name: 'Cedi', rate: 15 }, // Ghana
    'ZA': { currency: 'ZAR', symbol: 'R', name: 'Rand', rate: 25 }, // Afrique du Sud
    'MA': { currency: 'MAD', symbol: 'DH', name: 'Dirham', rate: 13 }, // Maroc
    'DZ': { currency: 'DZD', symbol: 'DA', name: 'Dinar', rate: 180 }, // Algérie
    'TN': { currency: 'TND', symbol: 'DT', name: 'Dinar', rate: 4 }, // Tunisie
    'EG': { currency: 'EGP', symbol: 'E£', name: 'Livre', rate: 65 }, // Égypte
    
    // ======== EUROPE ========
    'FR': { currency: 'EUR', symbol: '€', name: 'Euro', rate: 0.0015 }, // France
    'DE': { currency: 'EUR', symbol: '€', name: 'Euro', rate: 0.0015 }, // Allemagne
    'IT': { currency: 'EUR', symbol: '€', name: 'Euro', rate: 0.0015 }, // Italie
    'ES': { currency: 'EUR', symbol: '€', name: 'Euro', rate: 0.0015 }, // Espagne
    'PT': { currency: 'EUR', symbol: '€', name: 'Euro', rate: 0.0015 }, // Portugal
    'BE': { currency: 'EUR', symbol: '€', name: 'Euro', rate: 0.0015 }, // Belgique
    'CH': { currency: 'CHF', symbol: 'Fr', name: 'Franc Suisse', rate: 0.0018 }, // Suisse
    'GB': { currency: 'GBP', symbol: '£', name: 'Livre Sterling', rate: 0.0013 }, // UK
    'RU': { currency: 'RUB', symbol: '₽', name: 'Rouble', rate: 0.15 }, // Russie
    
    // ======== AMÉRIQUES ========
    'US': { currency: 'USD', symbol: '$', name: 'Dollar US', rate: 0.0017 }, // États-Unis
    'CA': { currency: 'CAD', symbol: 'C$', name: 'Dollar Canadien', rate: 0.0023 }, // Canada
    'MX': { currency: 'MXN', symbol: '$', name: 'Peso', rate: 0.03 }, // Mexique
    'BR': { currency: 'BRL', symbol: 'R$', name: 'Real', rate: 0.009 }, // Brésil
    'AR': { currency: 'ARS', symbol: '$', name: 'Peso', rate: 0.5 }, // Argentine
    
    // ======== ASIE ========
    'CN': { currency: 'CNY', symbol: '¥', name: 'Yuan', rate: 0.012 }, // Chine
    'JP': { currency: 'JPY', symbol: '¥', name: 'Yen', rate: 0.25 }, // Japon
    'IN': { currency: 'INR', symbol: '₹', name: 'Roupie', rate: 0.14 }, // Inde
    'PK': { currency: 'PKR', symbol: '₨', name: 'Roupie', rate: 0.5 }, // Pakistan
    'BD': { currency: 'BDT', symbol: '৳', name: 'Taka', rate: 0.2 }, // Bangladesh
    'VN': { currency: 'VND', symbol: '₫', name: 'Dong', rate: 40 }, // Vietnam
    'TH': { currency: 'THB', symbol: '฿', name: 'Baht', rate: 0.06 }, // Thaïlande
    'SG': { currency: 'SGD', symbol: 'S$', name: 'Dollar', rate: 0.0023 }, // Singapour
    'MY': { currency: 'MYR', symbol: 'RM', name: 'Ringgit', rate: 0.008 }, // Malaisie
    'ID': { currency: 'IDR', symbol: 'Rp', name: 'Rupiah', rate: 26 }, // Indonésie
    'PH': { currency: 'PHP', symbol: '₱', name: 'Peso', rate: 0.1 }, // Philippines
    'KR': { currency: 'KRW', symbol: '₩', name: 'Won', rate: 2.3 }, // Corée
    
    // ======== MOYEN-ORIENT ========
    'SA': { currency: 'SAR', symbol: '﷼', name: 'Riyal', rate: 0.0064 }, // Arabie
    'AE': { currency: 'AED', symbol: 'د.إ', name: 'Dirham', rate: 0.0063 }, // Dubaï
    'QA': { currency: 'QAR', symbol: '﷼', name: 'Riyal', rate: 0.0062 }, // Qatar
    'KW': { currency: 'KWD', symbol: 'د.ك', name: 'Dinar', rate: 0.0005 }, // Koweït
    'TR': { currency: 'TRY', symbol: '₺', name: 'Lire', rate: 0.055 }, // Turquie
    'IL': { currency: 'ILS', symbol: '₪', name: 'Shekel', rate: 0.0063 }, // Israël
    
    // ======== OCÉANIE ========
    'AU': { currency: 'AUD', symbol: 'A$', name: 'Dollar', rate: 0.0026 }, // Australie
    'NZ': { currency: 'NZD', symbol: 'NZ$', name: 'Dollar', rate: 0.0028 } // NZ
};

// Adresses rapides par pays
const quickAddressesByCountry = {
    // Afrique
    'CI': { home: 'Abidjan, Côte d\'Ivoire', work: 'Plateau, Abidjan', airport: 'Aéroport FHB' },
    'SN': { home: 'Dakar, Sénégal', work: 'Plateau, Dakar', airport: 'Aéroport Blaise Diagne' },
    'CD': { home: 'Kinshasa, RDC', work: 'Gombe, Kinshasa', airport: 'Aéroport N\'Djili' },
    'CM': { home: 'Douala, Cameroun', work: 'Bonanjo, Douala', airport: 'Aéroport Douala' },
    'NG': { home: 'Lagos, Nigeria', work: 'Victoria Island', airport: 'Aéroport Murtala' },
    'ZA': { home: 'Johannesburg', work: 'Sandton', airport: 'Aéroport OR Tambo' },
    'MA': { home: 'Casablanca', work: 'Centre Ville', airport: 'Aéroport Mohammed V' },
    'EG': { home: 'Le Caire', work: 'Centre Ville', airport: 'Aéroport du Caire' },
    
    // Europe
    'FR': { home: 'Paris', work: 'La Défense', airport: 'Aéroport CDG' },
    'GB': { home: 'Londres', work: 'City', airport: 'Heathrow' },
    'DE': { home: 'Berlin', work: 'Mitte', airport: 'Aéroport BER' },
    'IT': { home: 'Rome', work: 'Centro', airport: 'Fiumicino' },
    'ES': { home: 'Madrid', work: 'Centro', airport: 'Barajas' },
    
    // Amériques
    'US': { home: 'New York', work: 'Manhattan', airport: 'JFK' },
    'CA': { home: 'Toronto', work: 'Downtown', airport: 'Pearson' },
    'BR': { home: 'São Paulo', work: 'Centro', airport: 'Guarulhos' },
    
    // Asie
    'JP': { home: 'Tokyo', work: 'Shinjuku', airport: 'Narita' },
    'CN': { home: 'Pékin', work: 'Centre', airport: 'Aéroport de Pékin' },
    'IN': { home: 'Mumbai', work: 'Bandra', airport: 'Aéroport Mumbai' },
    'TH': { home: 'Bangkok', work: 'Sukhumvit', airport: 'Suvarnabhumi' }
};

// ============================================
// 2. DÉTECTION DE LA POSITION (MONDIALE)
// ============================================

async function detectUserLocation() {
    const methods = [
        { name: 'GPS', fn: detectByGPS },
        { name: 'IP', fn: detectByIP },
        { name: 'Timezone', fn: detectByTimezone }
    ];

    for (const method of methods) {
        try {
            console.log(`🌍 Tentative: ${method.name}...`);
            const result = await method.fn();
            if (result) {
                console.log(`✅ Succès ${method.name}:`, result);
                return result;
            }
        } catch (error) {
            console.log(`❌ Échec ${method.name}:`, error);
        }
    }

    // Fallback ultime (Afrique)
    console.log('⚠️ Fallback ultime: Afrique');
    return {
        coords: [5.336, -4.0267], // Abidjan
        country: "Côte d'Ivoire",
        countryCode: 'CI',
        city: 'Abidjan',
        method: 'fallback'
    };
}

function detectByGPS() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject('GPS non supporté');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                resolve({
                    coords: [position.coords.latitude, position.coords.longitude],
                    method: 'gps',
                    accuracy: position.coords.accuracy
                });
            },
            (error) => {
                let message = 'Erreur GPS';
                if (error.code === 1) message = 'Permission refusée';
                if (error.code === 2) message = 'Position indisponible';
                if (error.code === 3) message = 'Délai dépassé';
                reject(message);
            },
            {
                enableHighAccuracy: true,
                timeout: 8000,
                maximumAge: 0
            }
        );
    });
}

async function detectByIP() {
    try {
        const response = await fetch('https://ipapi.co/json/');
        const data = await response.json();
        
        if (data.error) throw new Error(data.reason || 'Erreur API');

        return {
            coords: [data.latitude, data.longitude],
            country: data.country_name,
            countryCode: data.country_code,
            city: data.city,
            method: 'ip'
        };
    } catch (error) {
        try {
            const response = await fetch('https://ipinfo.io/json');
            const data = await response.json();
            const loc = data.loc.split(',');
            
            return {
                coords: [parseFloat(loc[0]), parseFloat(loc[1])],
                country: data.country,
                countryCode: data.country,
                city: data.city,
                method: 'ip-fallback'
            };
        } catch (e) {
            throw new Error('Tous les services IP ont échoué');
        }
    }
}

function detectByTimezone() {
    return new Promise((resolve) => {
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const language = navigator.language || navigator.userLanguage;
        
        let coords, country, countryCode;
        
        if (timezone.includes('Africa')) {
            if (timezone.includes('Casablanca')) {
                coords = [33.5731, -7.5898];
                country = 'Maroc';
                countryCode = 'MA';
            } else if (timezone.includes('Johannesburg')) {
                coords = [-26.2041, 28.0473];
                country = 'Afrique du Sud';
                countryCode = 'ZA';
            } else if (timezone.includes('Lagos')) {
                coords = [6.5244, 3.3792];
                country = 'Nigeria';
                countryCode = 'NG';
            } else if (timezone.includes('Nairobi')) {
                coords = [-1.2864, 36.8172];
                country = 'Kenya';
                countryCode = 'KE';
            } else if (timezone.includes('Kinshasa')) {
                coords = [-4.4419, 15.2663];
                country = 'République Démocratique du Congo';
                countryCode = 'CD';
            } else {
                coords = [5.336, -4.0267];
                country = "Côte d'Ivoire";
                countryCode = 'CI';
            }
        } else if (timezone.includes('Europe')) {
            coords = [48.8566, 2.3522];
            country = 'France';
            countryCode = 'FR';
        } else if (timezone.includes('America')) {
            coords = [40.7128, -74.0060];
            country = 'États-Unis';
            countryCode = 'US';
        } else if (timezone.includes('Asia')) {
            coords = [35.6895, 139.6917];
            country = 'Japon';
            countryCode = 'JP';
        } else {
            coords = [0, 0];
            country = 'International';
            countryCode = 'INT';
        }
        
        resolve({
            coords,
            country,
            countryCode,
            method: 'timezone'
        });
    });
}

// ============================================
// 3. CONFIGURATION DE LA DEVISE
// ============================================

function setCurrencyFromCountry(countryCode) {
    if (worldCurrencies[countryCode]) {
        const currencyData = worldCurrencies[countryCode];
        userCurrency = currencyData.currency;
        currencySymbol = currencyData.symbol;
        exchangeRate = currencyData.rate;
        
        console.log(`💰 Devise détectée: ${userCurrency} (${currencySymbol}) - Taux: ${exchangeRate}`);
        
        // Mettre à jour l'affichage des prix
        updateAllPricesDisplay();
        
        return true;
    } else {
        console.log(`⚠️ Pays ${countryCode} non trouvé, utilisation FCFA par défaut`);
        userCurrency = 'FCFA';
        currencySymbol = 'F';
        exchangeRate = 1;
        return false;
    }
}

// Mettre à jour tous les prix sans changer la distance
function updateAllPricesDisplay() {
    const vehicleCards = document.querySelectorAll('.vehicle-card-2026');
    
    vehicleCards.forEach(card => {
        const basePrice = parseInt(card.dataset.basePrice);
        const convertedPrice = convertPrice(basePrice);
        card.querySelector('.price').innerHTML = formatPrice(convertedPrice);
    });
    
    if (currentDistance > 0) {
        updatePrices();
    }
}

// ============================================
// 4. FONCTIONS DE CALCUL DES PRIX
// ============================================

function convertPrice(priceInFCFA) {
    if (userCurrency === 'FCFA') {
        return Math.round(priceInFCFA / 50) * 50;
    } else {
        const converted = priceInFCFA * exchangeRate;
        if (userCurrency === 'JPY') {
            return Math.round(converted);
        } else if (userCurrency === 'EUR' || userCurrency === 'USD' || userCurrency === 'GBP') {
            return Math.round(converted * 100) / 100;
        } else {
            return Math.round(converted);
        }
    }
}

function formatPrice(price) {
    if (userCurrency === 'FCFA') {
        return price.toFixed(0) + ' ' + currencySymbol;
    } else if (userCurrency === 'EUR' || userCurrency === 'USD' || userCurrency === 'GBP') {
        return currencySymbol + price.toFixed(2);
    } else if (userCurrency === 'JPY') {
        return '¥' + price.toFixed(0);
    } else {
        return price.toFixed(0) + ' ' + currencySymbol;
    }
}

// ============================================
// 5. ADRESSES RAPIDES PAR PAYS
// ============================================

function updateQuickAddresses(countryCode) {
    const addresses = quickAddressesByCountry[countryCode] || quickAddressesByCountry['CI'];
    const container = document.getElementById('quickAddresses');
    
    container.innerHTML = `
        <div class="chip" data-address="${addresses.home}">
            <i class="fas fa-home"></i>
            <span>Maison</span>
        </div>
        <div class="chip" data-address="${addresses.work}">
            <i class="fas fa-briefcase"></i>
            <span>Travail</span>
        </div>
        <div class="chip" data-address="${addresses.airport}">
            <i class="fas fa-plane"></i>
            <span>Aéroport</span>
        </div>
    `;
    
    // Réattacher les événements
    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const address = this.querySelector('span').innerHTML;
            document.getElementById('destinationInput').value = address;
            searchLocations(address);
        });
    });
}

// ============================================
// 6. INITIALISATION DE LA CARTE
// ============================================

async function initMap() {
    map = L.map('map', {
        zoomControl: false,
        attributionControl: false
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    try {
        const location = await detectUserLocation();
        
        pickupCoords = location.coords;
        userCountry = location.country;
        userCountryCode = location.countryCode;
        
        setCurrencyFromCountry(userCountryCode);
        updateQuickAddresses(userCountryCode);
        
        map.setView(pickupCoords, CONFIG.defaultZoom);
        
        const currencyInfo = worldCurrencies[userCountryCode] || { currency: 'FCFA', symbol: 'F' };
        document.getElementById('currentLocationText').innerHTML = 
            `${location.city || 'Position'} • ${userCountry} • ${currencyInfo.currency}`;
        
        pickupMarker = L.marker(pickupCoords, {
            icon: L.divIcon({
                className: 'custom-marker',
                html: '<div class="marker-pulse"></div><i class="fas fa-circle" style="color: #4CAF50; font-size: 20px;"></i>',
                iconSize: [20, 20]
            })
        }).addTo(map).bindTooltip('Départ', { permanent: false });
        
        reverseGeocode(pickupCoords, 'pickup');
        
    } catch (error) {
        console.error('Erreur init:', error);
        fallbackToDefault();
    }
    
    map.on('click', function(e) {
        setDestination([e.latlng.lat, e.latlng.lng]);
    });
}

function fallbackToDefault() {
    pickupCoords = [5.336, -4.0267];
    map.setView(pickupCoords, 12);
    
    pickupMarker = L.marker(pickupCoords, {
        icon: L.divIcon({
            html: '<i class="fas fa-circle" style="color: #4CAF50; font-size: 20px;"></i>',
            iconSize: [20, 20]
        })
    }).addTo(map);
    
    document.getElementById('pickupDisplay').innerHTML = 'Abidjan';
    document.getElementById('pickupAddress').innerHTML = 'Abidjan, Côte d\'Ivoire';
    document.getElementById('currentLocationText').innerHTML = 'Abidjan (par défaut)';
    
    setCurrencyFromCountry('CI');
    updateQuickAddresses('CI');
}

// ============================================
// 7. GESTION DE LA DESTINATION ET TRAJET
// ============================================

async function setDestination(coords) {
    if (!pickupCoords) {
        alert('Veuillez attendre la localisation...');
        return;
    }

    destCoords = coords;
    
    if (destMarker) map.removeLayer(destMarker);
    
    destMarker = L.marker(coords, {
        icon: L.divIcon({
            className: 'custom-marker',
            html: '<i class="fas fa-map-pin" style="color: #ff6f61; font-size: 30px;"></i>',
            iconSize: [30, 30]
        })
    }).addTo(map).bindTooltip('Destination', { permanent: false });
    
    map.fitBounds([pickupCoords, coords], { 
        padding: [50, 50],
        maxZoom: 15
    });
    
    await calculateAndDrawRoute(pickupCoords, coords);
    reverseGeocode(coords, 'destination');
    
    document.getElementById('orderBtn').disabled = false;
    document.getElementById('routeLegend').style.display = 'flex';
}

async function calculateAndDrawRoute(start, end) {
    const url = `${CONFIG.osrmServer}/driving/${start[1]},${start[0]};${end[1]},${end[0]}?overview=full&geometries=geojson`;
    
    try {
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.routes && data.routes[0]) {
            const route = data.routes[0];
            
            currentDistance = route.distance / 1000;
            currentDuration = route.duration / 60;
            
            document.getElementById('legendDistance').innerHTML = currentDistance.toFixed(1) + ' km';
            
            if (routeLine) map.removeLayer(routeLine);
            
            const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
            routeLine = L.polyline(coordinates, {
                color: '#ff6f61',
                weight: 5,
                opacity: 0.8,
                lineCap: 'round',
                lineJoin: 'round'
            }).addTo(map);
            
            updatePrices();
        }
    } catch (error) {
        console.error('Erreur calcul trajet:', error);
        drawStraightLine(start, end);
    }
}

function drawStraightLine(start, end) {
    if (routeLine) map.removeLayer(routeLine);
    
    routeLine = L.polyline([start, end], {
        color: '#ff6f61',
        weight: 4,
        opacity: 0.6,
        dashArray: '5, 10'
    }).addTo(map);
    
    const R = 6371;
    const dLat = (end[0] - start[0]) * Math.PI / 180;
    const dLon = (end[1] - start[1]) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(start[0] * Math.PI / 180) * Math.cos(end[0] * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    currentDistance = R * c;
    currentDuration = currentDistance * 2;
    
    document.getElementById('legendDistance').innerHTML = currentDistance.toFixed(1) + ' km (approx)';
    updatePrices();
}

// ============================================
// 8. MISE À JOUR DES PRIX AVEC DEVISE
// ============================================

function updatePrices() {
    const vehicleCards = document.querySelectorAll('.vehicle-card-2026');
    
    vehicleCards.forEach(card => {
        const basePrice = parseInt(card.dataset.basePrice);
        const pricePerKm = parseInt(card.dataset.priceKm);
        const totalFCFA = basePrice + (pricePerKm * currentDistance);
        const convertedPrice = convertPrice(totalFCFA);
        
        card.querySelector('.price').innerHTML = formatPrice(convertedPrice);
        
        if (card.dataset.vehicle === selectedVehicle) {
            document.getElementById('tripSummary').style.display = 'block';
            document.getElementById('finalPrice').innerHTML = formatPrice(convertedPrice);
            document.getElementById('distanceEstimate').innerHTML = currentDistance.toFixed(1) + ' km';
            document.getElementById('durationEstimate').innerHTML = Math.round(currentDuration) + ' min';
            document.getElementById('totalEstimate').innerHTML = formatPrice(convertedPrice);
            
            document.getElementById('legendPickup').innerHTML = document.getElementById('pickupDisplay').innerHTML;
            document.getElementById('legendDestination').innerHTML = document.getElementById('destinationInput').value || 'Destination';
        }
    });
}

// ============================================
// 9. RECHERCHE D'ADRESSES
// ============================================

async function searchLocations(query) {
    if (query.length < 2) {
        document.getElementById('suggestions').style.display = 'none';
        return;
    }

    try {
        const countryFilter = userCountryCode ? `&countrycodes=${userCountryCode.toLowerCase()}` : '';
        const response = await fetch(
            `${CONFIG.nominatimServer}/search?format=json&q=${encodeURIComponent(query)}${countryFilter}&limit=5`
        );
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
        } else {
            suggestions.style.display = 'none';
        }
    } catch (error) {
        console.error('Erreur recherche:', error);
    }
}

async function reverseGeocode(coords, type) {
    try {
        const response = await fetch(
            `${CONFIG.nominatimServer}/reverse?format=json&lat=${coords[0]}&lon=${coords[1]}`
        );
        const data = await response.json();
        
        if (type === 'pickup') {
            document.getElementById('pickupDisplay').innerHTML = data.display_name?.split(',')[0] || 'Position actuelle';
            document.getElementById('pickupAddress').innerHTML = data.display_name || 'Position actuelle';
        } else {
            document.getElementById('destinationInput').value = data.display_name?.split(',')[0] || 'Destination';
            document.getElementById('destinationAddress').innerHTML = data.display_name || 'Destination';
        }
    } catch (error) {
        console.error('Erreur géocodage:', error);
    }
}

// ============================================
// 10. EVENT LISTENERS
// ============================================

function initEventListeners() {
    let timeout;
    document.getElementById('destinationInput').addEventListener('input', function(e) {
        clearTimeout(timeout);
        timeout = setTimeout(() => searchLocations(e.target.value), 300);
    });

    document.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function() {
            const address = this.querySelector('span').innerHTML;
            document.getElementById('destinationInput').value = address;
            searchLocations(address);
        });
    });

    document.querySelectorAll('.vehicle-card-2026').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.vehicle-card-2026').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            selectedVehicle = this.dataset.vehicle;
            if (currentDistance > 0) updatePrices();
        });
    });

    document.getElementById('locateMe').addEventListener('click', function() {
        if (pickupCoords) map.setView(pickupCoords, 16);
    });

    document.getElementById('zoomIn').addEventListener('click', () => map.zoomIn());
    document.getElementById('zoomOut').addEventListener('click', () => map.zoomOut());
    
    document.getElementById('swapLocations').addEventListener('click', function() {
        if (pickupCoords && destCoords) {
            const temp = pickupCoords;
            pickupCoords = destCoords;
            destCoords = temp;
            
            map.removeLayer(pickupMarker);
            map.removeLayer(destMarker);
            
            pickupMarker = L.marker(pickupCoords, {
                icon: L.divIcon({
                    html: '<div class="marker-pulse"></div><i class="fas fa-circle" style="color: #4CAF50; font-size: 20px;"></i>',
                    iconSize: [20, 20]
                })
            }).addTo(map);
            
            destMarker = L.marker(destCoords, {
                icon: L.divIcon({
                    html: '<i class="fas fa-map-pin" style="color: #ff6f61; font-size: 30px;"></i>',
                    iconSize: [30, 30]
                })
            }).addTo(map);
            
            calculateAndDrawRoute(pickupCoords, destCoords);
            reverseGeocode(pickupCoords, 'pickup');
            reverseGeocode(destCoords, 'destination');
        }
    });

    document.getElementById('orderBtn').addEventListener('click', function() {
        const vehicle = document.querySelector(`.vehicle-card-2026[data-vehicle="${selectedVehicle}"] h4`).innerHTML;
        const total = document.getElementById('totalEstimate').innerHTML;
        const distance = document.getElementById('distanceEstimate').innerHTML;
        const duration = document.getElementById('durationEstimate').innerHTML;
        
        alert(`✅ Course confirmée !\n\n` +
              `Véhicule: ${vehicle}\n` +
              `Distance: ${distance}\n` +
              `Durée: ${duration}\n` +
              `Prix: ${total}\n` +
              `Pays: ${userCountry}\n` +
              `Devise: ${userCurrency}`);
    });
}

// ============================================
// 11. INITIALISATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    initEventListeners();
});
</script>