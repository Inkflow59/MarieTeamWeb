<?php
include("BackCore.php");

// Récupérer les données JSON envoyées
$data = json_decode(file_get_contents('php://input'), true);

// Vérifiez si les données sont valides
if (is_null($data)) {
    echo json_encode(['success' => false, 'message' => 'Données JSON invalides.']);
    exit();
}

// Récupérer les valeurs depuis les données envoyées
$numTra = $data['numTra'] ?? null;
$prixTotal = $data['prixTotal'] ?? null;
$nomRes = $data['nomRes'] ?? null; // Récupérer le nom de l'utilisateur
$adresse = $data['adresse'] ?? null; // Récupérer l'adresse de l'utilisateur
$codePostal = $data['codePostal'] ?? null; // Récupérer le code postal
$ville = $data['ville'] ?? null; // Récupérer la ville
$quantites = $data['quantites'] ?? []; // Récupérer les quantités
$idTypeArray = [1, 2, 3, 4, 5, 6, 7]; // Exemple d'idType correspondant à chaque index

// Vérifiez que toutes les valeurs nécessaires sont présentes
if (is_null($numTra) || is_null($prixTotal) || is_null($nomRes) || is_null($adresse) || is_null($codePostal) || is_null($ville)) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes.']);
    exit();
}

// Préparer les types et quantités pour la réservation
$typesQuantites = [];
foreach ($quantites as $index => $quantite) {
    if ($quantite > 0) {
        $typesQuantites[$idTypeArray[$index]] = $quantite; // Associer l'idType à la quantité
    }
}

// Appeler la fonction de réservation
$reservationSuccess = reserverTrajet($numRes, $nomRes, $adresse, $codePostal, $ville, $numTra, $typesQuantites);

if ($reservationSuccess) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Échec de la réservation.']);
}
?>