<?php
include("php/BackCore.php");
include 'module/header.php';
// Récupérer le numTra depuis la requête POST ou GET
$numTra = isset($_POST['numTra']) ? $_POST['numTra'] : (isset($_GET['numTra']) ? $_GET['numTra'] : null);

// Vérifier si numTra est valide et récupérer les prix
if ($numTra) {
    // Appeler la fonction pour obtenir les tarifs
    $prixList = getTarifsByNumTra($numTra);
} else {
    // Gérer le cas où numTra n'est pas valide
    $prixList = [];
    // Rediriger vers la page de réservation si aucun numTra n'est fourni
    header('Location: reservation.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/selection.css">
    <title>Sélection des billets - MarieTeam</title>
    <script src="js/getPrixTotal.js"></script>
</head>

<body>
    <?php include 'module/header.php'; ?>
    
    <div class="bg"></div>

    <div class="center">
        <h1>Sélection des billets</h1>
        
        <!-- Passagers -->
        <div class="grid-section">
            <div class="section-title">Passagers</div>
            
            <!-- Adulte -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 49 56">
                            <path d="M12.9756 19.8892C13.6359 26.7257 18.0576 31.2408 24.3276 31.2408C30.5974 31.2408 35.0186 26.7255 35.6792 19.8892L36.6153 12.503C37.196 5.24625 31.2744 0 24.3276 0C17.3806 0 11.459 5.24625 12.0393 12.503L12.9756 19.8892Z" />
                            <path d="M48.6142 44.7979L48.166 41.9444C47.7907 39.5518 46.2869 37.4863 44.126 36.3943L34.6956 31.6255C34.4004 31.4763 34.16 31.2512 33.9586 31C31.2285 33.8866 27.9117 35.5844 24.3316 35.5844C20.7517 35.5844 17.4344 33.8866 14.7044 31C14.503 31.2512 14.2626 31.4763 13.9674 31.6255L4.53713 36.3943C2.37595 37.4863 0.872438 39.5518 0.497086 41.9444L0.048727 44.7979C-0.111 45.8165 0.0998208 47.3113 1.01224 48.0586C2.23216 49.0563 6.73861 55.2781 24.3316 55.2781C41.9244 55.2781 46.4306 49.0563 47.6505 48.0586C48.5633 47.3113 48.7739 45.8165 48.6142 44.7979Z" />
                        </svg>
                        <div class="ticket-info">
                            <p class="type">Adulte</p>
                            <p class="description">12 ans et plus</p>
                        </div>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[1]) ? $prixList[1] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre d'adultes" />
                    </div>
                </div>
            </div>

            <!-- Enfant -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 60">
                            <path d="M21.5625 8.4375C21.5625 3.77754 25.34 0 30 0C34.6599 0 38.4375 3.77754 38.4375 8.4375C38.4375 13.0973 34.6599 16.875 30 16.875C25.34 16.875 21.5625 13.0973 21.5625 8.4375ZM51.4016 8.5984C49.9372 7.13391 47.5627 7.13391 46.0984 8.5984L35.9467 18.75H24.0532L13.9016 8.5984C12.4372 7.13391 10.0627 7.13391 8.59837 8.5984C7.13388 10.0629 7.13388 12.4372 8.59837 13.9017L19.6875 24.9907V56.25C19.6875 58.3211 21.3664 60 23.4375 60H25.3125C27.3835 60 29.0625 58.3211 29.0625 56.25V43.125H30.9375V56.25C30.9375 58.3211 32.6164 60 34.6875 60H36.5625C38.6335 60 40.3125 58.3211 40.3125 56.25V24.9907L51.4016 13.9016C52.8661 12.4371 52.8661 10.0629 51.4016 8.5984Z" fill="black"/>
                        </svg>
                        <div class="ticket-info">
                            <p class="type">Enfant</p>
                            <p class="description">De 4 à 11 ans</p>
                        </div>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[2]) ? $prixList[2] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre d'enfants" />
                    </div>
                </div>
            </div>

            <!-- Senior -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 49 56" fill="none">
                            <path d="M12.9756 19.8892C13.6359 26.7257 18.0576 31.2408 24.3276 31.2408C30.5974 31.2408 35.0186 26.7255 35.6792 19.8892L36.6153 12.503C37.196 5.24625 31.2744 0 24.3276 0C17.3806 0 11.459 5.24625 12.0393 12.503L12.9756 19.8892Z" fill="black" />
                            <path d="M48.6142 44.7979L48.166 41.9444C47.7907 39.5518 46.2869 37.4863 44.126 36.3943L34.6956 31.6255C34.4004 31.4763 34.16 31.2512 33.9586 31C31.2285 33.8866 27.9117 35.5844 24.3316 35.5844C20.7517 35.5844 17.4344 33.8866 14.7044 31C14.503 31.2512 14.2626 31.4763 13.9674 31.6255L4.53713 36.3943C2.37595 37.4863 0.872438 39.5518 0.497086 41.9444L0.048727 44.7979C-0.111 45.8165 0.0998208 47.3113 1.01224 48.0586C2.23216 49.0563 6.73861 55.2781 24.3316 55.2781C41.9244 55.2781 46.4306 49.0563 47.6505 48.0586C48.5633 47.3113 48.7739 45.8165 48.6142 44.7979Z" fill="black" />
                        </svg>
                        <div class="ticket-info">
                            <p class="type">Senior</p>
                            <p class="description">65 ans et plus</p>
                        </div>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[3]) ? $prixList[3] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre de seniors" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Véhicules -->
        <div class="grid-section">
            <div class="section-title">Véhicules</div>
            
            <!-- Voiture -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <div class="svg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.25 3.75L6.25001 18.75H0V30H3.75V56.25H11.25V48.75H48.75V56.25H56.25V30H60V18.75H53.7499L48.75 3.75H11.25ZM15 33.75C12.929 33.75 11.25 35.4289 11.25 37.5C11.25 39.5711 12.929 41.25 15 41.25C17.0711 41.25 18.75 39.5711 18.75 37.5C18.75 35.4289 17.0711 33.75 15 33.75ZM43.3444 11.25H16.6557L11.6557 26.25H48.3442L43.3444 11.25ZM45 33.75C42.9289 33.75 41.25 35.4289 41.25 37.5C41.25 39.5711 42.9289 41.25 45 41.25C47.0711 41.25 48.75 39.5711 48.75 37.5C48.75 35.4289 47.0711 33.75 45 33.75Z" fill="black"/>
                            </svg>
                        </div>
                        <p class="type">Voiture</p>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[4]) ? $prixList[4] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre de voitures" />
                    </div>
                </div>
            </div>

            <!-- Moto -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <div class="svg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M11.25 3.75L6.25001 18.75H0V30H3.75V56.25H11.25V48.75H48.75V56.25H56.25V30H60V18.75H53.7499L48.75 3.75H11.25ZM15 33.75C12.929 33.75 11.25 35.4289 11.25 37.5C11.25 39.5711 12.929 41.25 15 41.25C17.0711 41.25 18.75 39.5711 18.75 37.5C18.75 35.4289 17.0711 33.75 15 33.75ZM43.3444 11.25H16.6557L11.6557 26.25H48.3442L43.3444 11.25ZM45 33.75C42.9289 33.75 41.25 35.4289 41.25 37.5C41.25 39.5711 42.9289 41.25 45 41.25C47.0711 41.25 48.75 39.5711 48.75 37.5C48.75 35.4289 47.0711 33.75 45 33.75Z" fill="black"/>
                            </svg>
                        </div>
                        <p class="type">Moto</p>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[5]) ? $prixList[5] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre de motos" />
                    </div>
                </div>
            </div>

            <!-- Poids lourd -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M41.25 7.5H0V48.75H7.5C7.5 52.8923 10.8579 56.25 15 56.25C19.1421 56.25 22.5 52.8923 22.5 48.75H37.5C37.5 52.8923 40.8577 56.25 45 56.25C49.1423 56.25 52.5 52.8923 52.5 48.75H60V30C60 23.7868 54.9634 18.75 48.75 18.75H41.25V7.5ZM41.25 26.25V33.75H52.5V26.25H41.25Z" fill="black"/>
                        </svg>
                        <p class="type">Poids lourd</p>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[6]) ? $prixList[6] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre de poids lourds" />
                    </div>
                </div>
            </div>

            <!-- Camping-car -->
            <div class="case">
                <div class="space">
                    <div class="espace1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M41.25 7.5H0V48.75H7.5C7.5 52.8923 10.8579 56.25 15 56.25C19.1421 56.25 22.5 52.8923 22.5 48.75H37.5C37.5 52.8923 40.8577 56.25 45 56.25C49.1423 56.25 52.5 52.8923 52.5 48.75H60V30C60 23.7868 54.9634 18.75 48.75 18.75H41.25V7.5ZM41.25 26.25V33.75H52.5V26.25H41.25Z" fill="black"/>
                        </svg>
                        <p class="type">Camping-car</p>
                    </div>
                    <div class="prix">
                        <p class="type"><?php echo isset($prixList[7]) ? $prixList[7] . ' €' : 'Prix non disponible'; ?></p>
                        <input type="number" min="0" value="0" class="quantite" aria-label="Nombre de camping-cars" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Total et Formulaire -->
        <div class="grid-section">
            <div class="casePrix">
                <div class="space">
                    <div class="espace1">
                        <p class="type">Total de la réservation :</p>
                        <p class="type" id="prixTotal">0.00 €</p>
                    </div>
                </div>
            </div>

            <div id="reservationForm">
                <h3>Informations de contact</h3>
                <form id="formData">
                    <div class="form-group">
                        <label for="nom">Nom de famille</label>
                        <input type="text" id="nom" name="nom" required placeholder="Votre nom">
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" id="adresse" name="adresse" required placeholder="Votre adresse">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="codePostal">Code Postal</label>
                            <input type="text" id="codePostal" name="codePostal" required placeholder="Code postal">
                        </div>
                        
                        <div class="form-group">
                            <label for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" required placeholder="Votre ville">
                        </div>
                    </div>

                    <input type="button" value="Procéder au paiement" onclick="submitAndPay()">
                </form>
            </div>
        </div>
    </div>

    <script src="js/getPrixTotal.js"></script>
    <script>
    function openForm() {
        document.getElementById('reservationForm').style.display = 'block';
    }

    function submitAndPay() {
        // Générer un numéro de réservation aléatoire de 10 chiffres
        const numRes = Math.floor(1000000000 + Math.random() * 9000000000);
        
        const nom = document.getElementById('nom').value;
        const adresse = document.getElementById('adresse').value;
        const codePostal = document.getElementById('codePostal').value;
        const ville = document.getElementById('ville').value;

        // Vérification des informations de réservation
        if (!nom || !adresse || !codePostal || !ville) {
            alert("Veuillez remplir tous les champs avant de continuer.");
            return; // Empêche la redirection si les champs ne sont pas remplis
        }

        // Vérification des quantités de billets
        const quantites = {};
        document.querySelectorAll('.quantite').forEach((input, index) => {
            quantites[index + 1] = parseInt(input.value) || 0;
        });
        
        const totalQuantites = Object.values(quantites).reduce((acc, curr) => acc + curr, 0);
        if (totalQuantites === 0) {
            alert("Veuillez sélectionner au moins un billet avant de continuer.");
            return;
        }

        // Calculer le prix total
        let prixTotal = 0;
        document.querySelectorAll('.prix .type').forEach((element, index) => {
            const prix = parseFloat(element.textContent.replace(' €', '')) || 0;
            prixTotal += prix * quantites[index + 1];
        });

        // Enregistrer le prix total dans un cookie
        document.cookie = "prixTotal=" + prixTotal.toFixed(2) + "; path=/"; // Stocke le prix total dans un cookie

        const numTra = <?php echo json_encode($numTra); ?>; // Récupérer numTra en PHP

        // Enregistrer les données dans le sessionStorage
        sessionStorage.setItem('reservationData', JSON.stringify({
            numRes,
            nom,
            adresse,
            codePostal,
            ville,
            quantites,
            prixTotal,
            numTra
        }));

        // Rediriger vers la page de paiement
        window.location.href = 'paiement.php'; // Redirection vers paiement.php
    }
    </script>

    <?php include 'module/footer.php'; ?>
</body>
</html>