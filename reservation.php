<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/reservation.css">
    <title>Réservation de traversées - MarieTeam</title>
</head>

<body class="bg-gray-50">
    <?php include 'module/header.php'; ?>
    <?php include "php/BackCore.php"; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="search-container">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Rechercher une traversée</h1>
            
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="form-group">
                    <label for="provenance" class="text-gray-900">Secteur</label>
                    <select id="provenance" name="provenance" class="text-gray-900">
                        <option value="">Tous les secteurs</option>
                        <option value="Méditerrannée">Méditerrannée</option>
                        <option value="Atlantique">Atlantique</option>
                        <option value="Manche">Manche</option>
                        <option value="Corse">Corse</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="depart" class="text-gray-900">Port de départ</label>
                    <select name="depart" id="depart" class="text-gray-900">
                        <option value="">Choisir un départ</option>
                        <?php
                        $port = getPorts();
                        foreach($port as $p) {
                            echo "<option value='".urlencode($p)."'>".$p."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="arrive" class="text-gray-900">Port d'arrivée</label>
                    <select name="arrive" id="arrive" class="text-gray-900">
                        <option value="">Choisir une arrivée</option>
                        <?php
                        foreach($port as $p) {
                            echo "<option value='".urlencode($p)."'>".$p."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="arrival_date" class="text-gray-900">Date de départ</label>
                    <input type="date" id="arrival_date" name="arrival_date" required class="text-gray-900">
                </div>

                <div class="col-span-full flex justify-center mt-4">
                    <button type="submit" class="search-button">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Rechercher une traversée
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reste du contenu -->
    <?php
// Nombre de trajets par page
$trajetsParPage = 5; // Modification à 5 trajets par page

// Détermine la page actuelle
$pageActuelle = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Réinitialiser la page à 1 si une recherche est effectuée
if (isset($_GET["provenance"]) || isset($_GET["arrival_date"]) || 
    isset($_GET["depart"]) || isset($_GET["arrive"])) {
    $pageActuelle = 1;
}

// Calculer l'offset pour la pagination
$offset = ($pageActuelle - 1) * $trajetsParPage;

// Récupération des trajets
$traversees = []; // Initialiser $traversees à un tableau vide
$totalTrajets = 0; // Initialiser $totalTrajets à 0 pour éviter l'erreur
if (!isset($_GET["provenance"])) {
    $traversees = getTraversees($trajetsParPage, $offset);
    $totalTrajets = getNombreTotalTraversees();
} else {
    $traversees = barreRecherche(
        urldecode($_GET["provenance"]), 
        urldecode($_GET["arrival_date"]), 
        urldecode($_GET["depart"]), 
        urldecode($_GET["arrive"]), 
        $trajetsParPage, 
        $offset
    );

    // Vérifiez si la fonction retourne un message d'erreur
    if (is_string($traversees)) {
        echo "<p>$traversees</p>"; // Affichez le message d'erreur
        $traversees = []; // Réinitialisez $traversees pour éviter les warnings
    } else {
        $totalTrajets = getNombreTotalRecherche(
            $_GET["provenance"], 
            $_GET["arrival_date"], 
            $_GET["depart"], 
            $_GET["arrive"]
        );
    }
}

// Assurez-vous que $traversees est un tableau
if (!is_array($traversees)) {
    echo "<p class='error-message'>Aucun trajet trouvé pour les critères spécifiés.</p>"; // Message d'erreur si aucun trajet n'est trouvé
    $traversees = []; // Réinitialisez à un tableau vide si ce n'est pas un tableau
}

// Fonction pour générer le HTML d'un trajet
function genererHTMLTrajet($t) {
    $place = getPlacesDisponiblesParCategorie($t['numTra']);
    $temps = getTempsTotalTraversee($t['numTra']);
    $prix = getPrixMinimumPourTraversee($t['numTra']);
    return "
    <main class='container mx-auto py-8'>
        <div class='space-y-6'>
            <div class='bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-2xl transition-shadow duration-300 overflow-hidden flex'>
                <div class='flex items-center justify-center w-24 bg-blue-100 dark:bg-blue-900'>
                    <svg xmlns='http://www.w3.org/2000/svg' width='60' height='61' viewBox='0 0 60 61' fill='none'>
                        <path d='M10 44.25L7.5 30.5L30 23L52.5 30.5L50 44.25M12.5 28.8332V18C12.5 15.2386 14.7386 13 17.5 13H42.5C45.2615 13 47.5 15.2386 47.5 18V28.8332M25 13V8C25 6.6193 26.1193 5.5 27.5 5.5H32.5C33.8807 5.5 35 6.6193 35 8V13M5 53C7.5 55.5 15 55.5 20 50.5C25 55.5 35 55.5 40 50.5C45 55.5 52.5 55.5 55 53' stroke='#1f2937' stroke-width='5' stroke-linecap='round' stroke-linejoin='round'/>
                    </svg>
                </div>
                <div class='w-[1px] bg-gray-300 h-auto my-6'></div>
                <div class='flex-1 p-6'>
                    <div class='mb-4'>
                        <h3 class='text-lg font-semibold text-gray-900'>".$t["port_depart"]." - ".$t["port_arrivee"]."</h3>
                        <span class='text-sm text-blue-600 font-medium'>À partir de ".$prix."€</span>
                    </div>
                    <div class='flex items-center justify-between mb-4'>
                        <div class='text-center'>
                            <p class='text-lg font-bold text-gray-800'>".substr($t['heure'], 0, 5)."</p>
                            <p class='text-sm text-gray-500'>Port de départ</p>
                        </div>
                        <div class='flex-1 mx-4 border-t border-gray-300'></div>
                        <div class='text-center'>
                            <p class='text-lg font-bold text-gray-800'>".getHeureArrivee($t['numTra'])."</p>
                            <p class='text-sm text-gray-500'>Port d'arrivée</p>
                        </div>
                    </div>
                    <div class='flex gap-6 text-sm text-gray-500'>
                        <div class='flex items-center gap-2'>
                            <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4 text-gray-400' fill='none' stroke='currentColor' stroke-width='2'>
                                <path d='M12 6v12m6-6H6' />
                            </svg>
                            <span>".$temps." de trajet</span>
                        </div>
                        <div class='flex items-center gap-2'>
                            <svg xmlns='http://www.w3.org/2000/svg' class='h-4 w-4 text-gray-400' fill='none' stroke='currentColor' stroke-width='2'>
                                <path d='M4 6h16M4 12h16M4 18h16' />
                            </svg>
                            <span>".$place["passagers"]." places</span>
                        </div>
                    </div>
                </div>
                <form method='POST' action='selection.php' onsubmit='showLoading(event)'>
                    <input type='hidden' name='numTra' value='".$t['numTra']."'>
                    <div class='p-6 flex items-center justify-center w-64'>
                        <button class='w-full py-3 px-4 text-sm font-semibold text-gray-900 border border-blue-500 rounded-lg hover:bg-blue-50 transition-colors duration-200'>
                            Sélectionner cette traversée
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>";
}

// Affichage des trajets
foreach ($traversees as $t) {
    echo genererHTMLTrajet($t);
}

// Pagination
$totalPages = ceil($totalTrajets / $trajetsParPage);
echo "<div class='pagination'>";

// Construction des paramètres de recherche pour les liens de pagination
$searchParams = '';
if (isset($_GET["provenance"])) {
    $searchParams .= '&provenance=' . urlencode($_GET["provenance"]);
    $searchParams .= '&arrival_date=' . urlencode($_GET["arrival_date"]);
    $searchParams .= '&depart=' . urlencode($_GET["depart"]);
    $searchParams .= '&arrive=' . urlencode($_GET["arrive"]);
}

// Modification des liens de pagination
if ($pageActuelle > 1) {
    echo "<a href='?page=1".$searchParams."' class='pagination-nav'>Première</a> ";
    echo "<a href='?page=".($pageActuelle-1).$searchParams."' class='pagination-nav'>Précédent</a> ";
}

// Ajout des boutons -5 et +5
if ($pageActuelle > 5) {
    echo "<a href='?page=".($pageActuelle-5).$searchParams."' class='pagination-nav'>-5</a> ";
}

// Affichage des 3 pages
for ($i = max(1, min($pageActuelle - 1, $totalPages - 2)); 
     $i <= min($totalPages, max(3, $pageActuelle + 1)); 
     $i++) {
    $activeClass = ($i === $pageActuelle) ? "active" : "";
    echo "<a href='?page=$i".$searchParams."' class='$activeClass'>$i</a> ";
}

// Ajout d'un lien vers la dernière page
if ($totalPages > 3) {
    echo "<a href='?page=$totalPages".$searchParams."' class='pagination-nav'>Dernière</a> ";
}

// Ajout du bouton +5 à droite
if ($pageActuelle + 5 <= $totalPages) {
    echo "<a href='?page=".($pageActuelle+5).$searchParams."' class='pagination-nav'>+5</a> ";
}

// Bouton suivant
if ($pageActuelle < $totalPages) {
    echo "<a href='?page=".($pageActuelle+1).$searchParams."' class='pagination-nav'>Suivant</a>";
}
echo "</div>";
?>


  <script src="js/localisation.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
  <div class="loading-overlay">
    <img src="img/chargement.gif" alt="Chargement..." class="loading-gif">
  </div>
  <script>
  function showLoading(event) {
    event.preventDefault();
    const form = event.target;
    const loadingOverlay = document.querySelector('.loading-overlay');
    
    loadingOverlay.classList.add('active');
    
    // Stockons les données du formulaire
    const formData = new FormData(form);
    
    setTimeout(() => {
        // Soumettons le formulaire en POST après le délai
        form.submit();
    }, 4000);
  }
  </script>
  <?php
  include 'module/footer.php';
  ?>
</body>
</html>