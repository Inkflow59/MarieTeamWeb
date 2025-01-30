<?php
include 'module/header.php';
include 'module/footer.php';

// Début de la structure HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Billet</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['billet'])) {
            $numBillet = $_POST['billet'];
            
            // Récupérer les informations de la réservation
            $billet = consulterReservation($numBillet); // Remplacez par la fonction appropriée

            if ($billet) {
                // Affichage des informations du billet
                echo "<h1 class='text-3xl font-bold mb-6'>Détails du Billet</h1>";
                echo "<div class='bg-white p-6 rounded shadow-lg'>";
                echo "<p><strong>Numéro de Billet:</strong> " . htmlspecialchars($billet['numRes']) . "</p>";
                echo "<p><strong>Nom:</strong> " . htmlspecialchars($billet['nomRes']) . "</p>";
                echo "<p><strong>Adresse:</strong> " . htmlspecialchars($billet['adresse']) . "</p>";
                echo "<p><strong>Code Postal:</strong> " . htmlspecialchars($billet['codePostal']) . "</p>";
                echo "<p><strong>Ville:</strong> " . htmlspecialchars($billet['ville']) . "</p>";
                
                // Récupérer les informations de la traversée
                $traversee = getInfosTraversee($billet['numTra']);
                
                if ($traversee) {
                    // Affichage des informations de la traversée
                    echo "<h2 class='text-2xl font-semibold mt-6'>Détails de la Traversée</h2>";
                    echo "<div class='bg-gray-50 p-6 rounded shadow-md'>";
                    echo "<p><strong>Date:</strong> " . htmlspecialchars($traversee['date']) . "</p>";
                    echo "<p><strong>Heure:</strong> " . htmlspecialchars($traversee['heure']) . "</p>";
                    echo "<p><strong>Bateau:</strong> " . htmlspecialchars($traversee['nomBat']) . "</p>";
                    echo "<p><strong>Port de Départ:</strong> " . htmlspecialchars($traversee['port_depart']) . "</p>";
                    echo "<p><strong>Port d'Arrivée:</strong> " . htmlspecialchars($traversee['port_arrivee']) . "</p>";
                    echo "<p><strong>Distance:</strong> " . htmlspecialchars($traversee['distance']) . " km</p>";
                    echo "<p><strong>Temps de Liaison:</strong> " . htmlspecialchars($traversee['tempsLiaison']) . "</p>";
                    echo "</div>";
                } else {
                    echo "<p class='text-red-500'>Aucune information de traversée trouvée.</p>";
                }
                
                // Affichage des types et quantités
                echo "<h2 class='text-2xl font-semibold mt-6'>Détails de la Réservation</h2>";
                echo "<ul class='list-disc pl-5 mb-4'>";
                foreach ($billet['types'] as $type) {
                    echo "<li>" . htmlspecialchars($type['libelleType']) . ": " . htmlspecialchars($type['quantite']) . "</li>";
                }
                echo "</ul>";
                echo "</div>"; // Fin du conteneur du billet
            } else {
                echo "<p class='text-red-500'>Aucun billet trouvé avec ce numéro.</p>";
            }
        } else {
            echo "<p class='text-red-500'>Veuillez soumettre le formulaire de recherche.</p>";
        }
        ?>
    </div>
<?php
include 'module/footer.php';
?>
</body>
</html> 