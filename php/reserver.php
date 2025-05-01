<?php
include("BackCore.php");

// Vérifier si les données de réservation ont été envoyées
if (!isset($_POST['reservationData'])) {
    header('Location: ../reservation.php?error=no_data');
    exit();
}

try {
    // Décoder les données JSON
    $reservationData = json_decode($_POST['reservationData'], true);
    
    // Journalisation pour le débogage
    error_log("Données reçues: " . print_r($reservationData, true));
    
    if (!$reservationData) {
        throw new Exception("Données de réservation invalides");
    }    // Vérifier que les idType sont valides avant de continuer
    $quantites = $reservationData['quantites'];
    foreach ($quantites as $idType => $quantite) {
        if ($quantite > 0) {  // Vérifier uniquement les types avec une quantité > 0
            // Vérifier si l'idType existe dans la table type
            $stmt = $db->prepare("SELECT idType FROM type WHERE idType = ? LIMIT 1");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête de vérification");
            }
            
            $stmt->bind_param("i", $idType);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Type de billet invalide : $idType");
            }
        }
    }

    // Générer un numéro de réservation numérique unique
    $numRes = mt_rand(100000, 999999);
    while (checkReservationExists($numRes)) {
        $numRes = mt_rand(100000, 999999);
    }

    // Extraire les données du tableau
    $nom = $reservationData['nom'];
    $adresse = $reservationData['adresse'];
    $codePostal = $reservationData['codePostal'];
    $ville = $reservationData['ville'];
    $numTra = $reservationData['numTra'];
    $quantites = $reservationData['quantites'];

    // Appeler la fonction reserverTrajet
    $resultat = reserverTrajet(
        $numRes,
        $nom,
        $adresse,
        $codePostal,
        $ville,
        $numTra,
        $quantites
    );

    if ($resultat) {
        // Stocker le numéro de réservation en session pour l'affichage ultérieur
        session_start();
        $_SESSION['derniere_reservation'] = $numRes;
        
        // Rediriger vers une page de confirmation
        header('Location: ../confirmation.php?success=1');
        exit();
    } else {
        throw new Exception("Échec de la réservation");
    }

} catch (Exception $e) {
    // Ajouter un log pour le débogage
    error_log("Erreur lors de la réservation : " . $e->getMessage());
    error_log("Trace complète: " . $e->getTraceAsString());
    
    // En cas d'erreur, rediriger vers la page de réservation avec un message d'erreur
    header('Location: ../reservation.php?error=' . urlencode("Erreur lors de la réservation : " . $e->getMessage()));
    exit();
}
?>