<?php
include("BackCore.php");

// Activation des erreurs PHP pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Démarrage de la session pour gérer le numéro de réservation
session_start();

// Récupération des données depuis POST
$reservationData = isset($_POST['reservationData']) ? json_decode($_POST['reservationData'], true) : null;

// Log des données reçues pour le débogage
error_log("Données POST reçues : " . print_r($_POST, true));
error_log("Données décodées : " . print_r($reservationData, true));

// Vérification du décodage JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Erreur de décodage JSON: " . json_last_error_msg());
    header("Location: ../paiement.php?error=invalid_data");
    exit();
}

// Vérification de la présence des données requises
if (!$reservationData || 
    !isset($reservationData['nom']) || 
    !isset($reservationData['adresse']) || 
    !isset($reservationData['codePostal']) || 
    !isset($reservationData['ville']) || 
    !isset($reservationData['numTra']) || 
    !isset($reservationData['quantites'])) {
    error_log("Données de réservation manquantes ou incomplètes");
    error_log("Données reçues : " . print_r($reservationData, true));
    header("Location: ../paiement.php?error=missing_data");
    exit();
}

// Génération du numéro de réservation unique
$numRes = time() . rand(1000, 9999);

// Traitement des quantités
$typesQuantites = [];
foreach ($reservationData['quantites'] as $typePassager => $quantite) {
    if (is_numeric($quantite) && $quantite > 0) {
        $typesQuantites[$typePassager] = intval($quantite);
    }
}

// Vérification qu'il y a au moins une quantité valide
if (empty($typesQuantites)) {
    error_log("Aucune quantité valide spécifiée");
    header("Location: ../paiement.php?error=invalid_quantities");
    exit();
}

try {
    // Log des paramètres de réservation
    error_log("Tentative de réservation :");
    error_log("numRes: " . $numRes);
    error_log("nom: " . $reservationData['nom']);
    error_log("numTra: " . $reservationData['numTra']);
    error_log("quantités: " . print_r($typesQuantites, true));

    // Appel de la fonction de réservation
    $reservationSuccess = reserverTrajet(
        $numRes,
        $reservationData['nom'],
        $reservationData['adresse'],
        $reservationData['codePostal'],
        $reservationData['ville'],
        $reservationData['numTra'],
        $typesQuantites
    );

    if ($reservationSuccess) {
        // Stockage du numéro de réservation en session
        $_SESSION['lastReservationNumber'] = $numRes;
        
        // Redirection vers la page de confirmation
        header("Location: ../confirmation.php?numRes=" . $numRes);
        exit();
    } else {
        error_log("Échec de la réservation sans exception");
        header("Location: ../paiement.php?error=reservation_failed");
        exit();
    }

} catch (Exception $e) {
    // Log détaillé de l'erreur
    error_log("Exception lors de la réservation:");
    error_log("Message: " . $e->getMessage());
    error_log("Fichier: " . $e->getFile());
    error_log("Ligne: " . $e->getLine());
    error_log("Trace: " . $e->getTraceAsString());

    // Redirection avec message d'erreur
    header("Location: ../paiement.php?error=exception&message=" . 
           urlencode($e->getMessage() ?: 'Erreur inconnue lors de la réservation'));
    exit();
}