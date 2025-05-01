<?php
include 'php/BackCore.php';
include 'module/header.php';

// Début de la structure HTML
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre Billet Maritime</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .ocean-gradient {
            background: linear-gradient(135deg, #1a4b8c 0%, #2c7bb6 100%);
        }
        .wave-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='20' viewBox='0 0 100 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M21.184 20c.357-.13.72-.264 1.088-.402l1.768-.661C33.64 15.347 39.647 14 50 14c10.271 0 15.362 1.222 24.629 4.928.955.383 1.869.74 2.75 1.072h6.225c-2.51-.73-5.139-1.691-8.233-2.928C65.888 13.278 60.13 12 50 12c-10.626 0-16.855 1.397-26.66 5.063l-1.767.662c-2.475.923-4.66 1.674-6.724 2.275h6.335zm0-20C13.258 2.892 8.077 4 0 4V2c5.744 0 9.951-.574 14.85-2h6.334zM77.38 0C85.239 2.966 90.502 4 100 4V2c-6.842 0-11.386-.542-16.396-2h-6.225zM0 14c8.44 0 13.718-1.21 22.272-4.402l1.768-.661C33.64 5.347 39.647 4 50 4c10.271 0 15.362 1.222 24.629 4.928C84.112 12.722 89.87 14 100 14v-2c-10.626 0-16.855-1.397-26.66-5.063l-1.767-.662C59.112 2.886 53.466 2 50 2 39.374 2 33.145 3.397 23.34 7.063l-1.767.662C13.205 10.897 7.559 12 0 12v2z' fill='%23ffffff10' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/path%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-blue-50">
    <?php include 'module/header.php'; ?>
    
    <div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 wave-pattern">
        <?php
        // Vérifie si le numéro de billet est fourni soit en POST soit en GET
        $numBillet = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['billet'])) {
            $numBillet = $_POST['billet'];
        } elseif (isset($_GET['billet'])) {
            $numBillet = $_GET['billet'];
        }

        if ($numBillet) {
            $billet = consulterReservation($numBillet);

            if ($billet) {
                ?>
                <div class="max-w-4xl mx-auto">
                    <div class="ocean-gradient text-white p-8 rounded-t-lg shadow-lg">
                        <div class="flex items-center justify-between mb-6">
                            <h1 class="text-3xl font-bold">Billet Maritime</h1>
                            <div class="text-right">
                                <span class="block text-sm opacity-75">N° Billet</span>
                                <span class="text-xl font-mono"><?php echo htmlspecialchars($billet['numRes']); ?></span>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h2 class="text-lg font-semibold border-b border-white/20 pb-2">Informations Passager</h2>
                                <p><span class="text-blue-200">Nom:</span> <?php echo htmlspecialchars($billet['nomRes']); ?></p>
                                <p><span class="text-blue-200">Adresse:</span> <?php echo htmlspecialchars($billet['adresse']); ?></p>
                                <p>
                                    <span class="text-blue-200">Localisation:</span>
                                    <?php echo htmlspecialchars($billet['codePostal']) . " " . htmlspecialchars($billet['ville']); ?>
                                </p>
                            </div>
                            
                            <?php
                            $traversee = getInfosTraversee($billet['numTra']);
                            if ($traversee) {
                            ?>
                            <div class="space-y-3">
                                <h2 class="text-lg font-semibold border-b border-white/20 pb-2">Détails de la Traversée</h2>
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75z"/>
                                    </svg>
                                    <span><?php echo htmlspecialchars($traversee['date']); ?> à <?php echo htmlspecialchars($traversee['heure']); ?></span>
                                </div>
                                <p><span class="text-blue-200">Navire:</span> <?php echo htmlspecialchars($traversee['nomBat']); ?></p>
                                <div class="flex items-center justify-between text-sm bg-white/10 rounded-lg p-3 mt-4">
                                    <div>
                                        <span class="block text-blue-200">Départ</span>
                                        <?php echo htmlspecialchars($traversee['port_depart']); ?>
                                    </div>
                                    <div class="text-center">
                                        <svg class="w-6 h-6 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 10a.75.75 0 01.75-.75h10.638L10.23 5.29a.75.75 0 111.04-1.08l5.5 5.25a.75.75 0 010 1.08l-5.5 5.25a.75.75 0 11-1.04-1.08l4.158-3.96H3.75A.75.75 0 013 10z"/>
                                        </svg>
                                        <span class="text-xs"><?php echo htmlspecialchars($traversee['distance']); ?> km</span>
                                    </div>
                                    <div>
                                        <span class="block text-blue-200">Arrivée</span>
                                        <?php echo htmlspecialchars($traversee['port_arrivee']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="bg-white rounded-b-lg shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Détails de la Réservation</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <?php foreach ($billet['types'] as $type): ?>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <span class="text-gray-600"><?php echo htmlspecialchars($type['libelleType']); ?></span>
                                <p class="text-2xl font-bold text-blue-600"><?php echo htmlspecialchars($type['quantite']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Bouton de téléchargement -->
                        <div class="mt-6 text-center">
                            <a href="generate_pdf.php?billet=<?php echo htmlspecialchars($numBillet); ?>" 
                               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Télécharger le Billet
                            </a>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo '<div class="max-w-4xl mx-auto bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                        <p class="font-bold">Erreur</p>
                        <p>Aucun billet trouvé avec ce numéro.</p>
                      </div>';
            }
        } else {
            echo '<div class="max-w-4xl mx-auto bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded">
                    <p>Veuillez soumettre le formulaire de recherche.</p>
                  </div>';
        }
        ?>
    </div>

    <?php include 'module/footer.php'; ?>
</body>
</html>