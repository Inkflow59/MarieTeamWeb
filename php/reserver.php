<?php
include("BackCore.php");

// Activation des erreurs PHP pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Récupérer les données
$data = isset($_POST['reservationData']) ? json_decode($_POST['reservationData'], true) : null;

// Log des données décodées
error_log("Données décodées: " . print_r($data, true));

// Vérification du décodage JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de décodage JSON: ' . json_last_error_msg()
    ]);
    exit();
}

// Vérification des données requises
$requiredFields = ['nom', 'adresse', 'codePostal', 'ville', 'numTra', 'quantites'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    echo json_encode([
        'success' => false,
        'message' => 'Champs manquants: ' . implode(', ', $missingFields)
    ]);
    exit();
}

// Génération du numéro de réservation
$numRes = time() . rand(1000, 9999);

// Préparation des données pour la réservation
$typesQuantites = [];
foreach ($data['quantites'] as $index => $quantite) {
    if ($quantite > 0) {
        $typesQuantites[$index] = $quantite;
    }
}

try {
    // Tentative de réservation
    $reservationSuccess = reserverTrajet(
        $numRes,
        $data['nom'],
        $data['adresse'],
        $data['codePostal'],
        $data['ville'],
        $data['numTra'],
        $typesQuantites
    );

    if ($reservationSuccess) {
        $_SESSION['lastReservationNumber'] = $numRes;
        header("Location: ../confirmation.php?numRes=" . $numRes);
        exit();
    } else {
        header("Location: ../paiement.php?error=reservation_failed");
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur lors de la réservation: " . $e->getMessage());
    header("Location: ../paiement.php?error=exception&message=" . urlencode($e->getMessage()));
    exit();
}