<?php
// Ce script génère un PDF contenant les détails d'une réservation
require_once('vendor/tcpdf/tcpdf.php');
require_once('php/BackCore.php');

// Vérifier si le numéro de réservation est fourni
if (!isset($_GET['billet']) || empty($_GET['billet'])) {
    die("Numéro de réservation manquant");
}

$numBillet = $_GET['billet'];
$reservation = consulterReservation($numBillet);

if (!$reservation) {
    die("Réservation non trouvée");
}

// Récupérer les détails de la traversée
$traverseeDetails = null;
if (isset($reservation['numTra'])) {
    // Utiliser la fonction existante
    $traverseeDetails = getInfosTraversee($reservation['numTra']);
}

// Créer un nouveau document PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Définir les informations du document
$pdf->SetCreator('MarieTeam');
$pdf->SetAuthor('MarieTeam');
$pdf->SetTitle('Billet de traversée n°' . $numBillet);
$pdf->SetSubject('Billet de traversée maritime');
$pdf->SetKeywords('MarieTeam, traversée, billet, réservation');

// Supprimer les en-têtes et pieds de page par défaut
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Définir les marges
$pdf->SetMargins(15, 15, 15);

// Définir l'auto-page-break
$pdf->SetAutoPageBreak(true, 15);

// Définir la police par défaut
$pdf->SetFont('helvetica', '', 10);

// Ajouter une page
$pdf->AddPage();

// Logo de MarieTeam
//$pdf->Image('img/logo.png', 15, 15, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

// Styles et contenu
$pdf->SetFont('helvetica', 'B', 20);
$pdf->SetTextColor(0, 102, 204);
$pdf->Cell(0, 15, 'MarieTeam', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(68, 68, 68);
$pdf->Cell(0, 10, 'Billet de traversée maritime', 0, 1, 'C');

$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetTextColor(51, 51, 51);
$pdf->Cell(0, 10, 'N° de réservation: ' . $numBillet, 0, 1, 'C');

$pdf->Ln(5);

// Informations sur le client
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(0, 102, 204);
$pdf->Cell(0, 10, 'Informations sur le client', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 11);
$pdf->SetTextColor(68, 68, 68);
$pdf->Cell(40, 7, 'Nom:', 0, 0, 'L');
$pdf->Cell(0, 7, $reservation['nomRes'], 0, 1, 'L');

$pdf->Cell(40, 7, 'Adresse:', 0, 0, 'L');
$pdf->Cell(0, 7, $reservation['adresse'], 0, 1, 'L');

$pdf->Cell(40, 7, 'Code postal:', 0, 0, 'L');
$pdf->Cell(0, 7, $reservation['codePostal'], 0, 1, 'L');

$pdf->Cell(40, 7, 'Ville:', 0, 0, 'L');
$pdf->Cell(0, 7, $reservation['ville'], 0, 1, 'L');

$pdf->Ln(5);

// Informations sur la traversée
if ($traverseeDetails) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetTextColor(0, 102, 204);
    $pdf->Cell(0, 10, 'Détails de la traversée', 0, 1, 'L');

    $pdf->SetFont('helvetica', '', 11);
    $pdf->SetTextColor(68, 68, 68);
    $pdf->Cell(40, 7, 'Date:', 0, 0, 'L');
    $pdf->Cell(0, 7, date('d/m/Y', strtotime($traverseeDetails['date'])), 0, 1, 'L');

    $pdf->Cell(40, 7, 'Heure de départ:', 0, 0, 'L');
    $pdf->Cell(0, 7, $traverseeDetails['heure'], 0, 1, 'L');

    $pdf->Cell(40, 7, 'Port de départ:', 0, 0, 'L');
    $pdf->Cell(0, 7, $traverseeDetails['port_depart'], 0, 1, 'L');    $pdf->Cell(40, 7, 'Port d\'arrivée:', 0, 0, 'L');
    $pdf->Cell(0, 7, $traverseeDetails['port_arrivee'], 0, 1, 'L');

    $pdf->Cell(40, 7, 'Bateau:', 0, 0, 'L');
    $pdf->Cell(0, 7, $traverseeDetails['nomBat'], 0, 1, 'L');
    
    $pdf->Cell(40, 7, 'Distance:', 0, 0, 'L');
    $pdf->Cell(0, 7, $traverseeDetails['distance'] . ' miles nautiques', 0, 1, 'L');
    
    $pdf->Cell(40, 7, 'Durée estimée:', 0, 0, 'L');
    $tempsLiaison = $traverseeDetails['tempsLiaison'];
    $heuresMinutes = substr($tempsLiaison, 0, 5); // Format HH:MM
    $pdf->Cell(0, 7, $heuresMinutes . ' heures', 0, 1, 'L');
    
    $pdf->Ln(5);
}

// Détails des billets
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(0, 102, 204);
$pdf->Cell(0, 10, 'Détails des billets', 0, 1, 'L');

$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetTextColor(68, 68, 68);
$pdf->Cell(70, 7, 'Type', 0, 0, 'L');
$pdf->Cell(30, 7, 'Quantité', 0, 0, 'R');
$pdf->Cell(30, 7, 'Prix unitaire', 0, 0, 'R');
$pdf->Cell(30, 7, 'Sous-total', 0, 1, 'R');

$pdf->SetFont('helvetica', '', 11);
$totalQuantity = 0;

if (isset($reservation['types']) && is_array($reservation['types'])) {
    foreach ($reservation['types'] as $type) {
        $pdf->Cell(100, 7, $type['libelleType'], 0, 0, 'L');
        $pdf->Cell(30, 7, $type['quantite'], 0, 1, 'R');
        $totalQuantity += $type['quantite'];
    }
}

$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(100, 7, 'Total', 0, 0, 'L');
$pdf->Cell(30, 7, $totalQuantity, 0, 1, 'R');

$pdf->Ln(10);

// Instructions et informations importantes
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetTextColor(0, 102, 204);
$pdf->Cell(0, 10, 'Informations importantes', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(68, 68, 68);
$pdf->MultiCell(0, 6, 'Veuillez vous présenter au port d\'embarquement au moins 30 minutes avant l\'heure de départ. Une pièce d\'identité sera exigée pour l\'embarquement. Ce billet est valable uniquement pour la date et l\'heure indiquées.', 0, 'L', false);

$pdf->Ln(5);

// QR code ou code à barres (optionnel)
$style = array(
    'border' => 2,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false,
    'module_width' => 1,
    'module_height' => 1
);

$pdf->write2DBarcode($numBillet, 'QRCODE,H', 150, 230, 40, 40, $style, 'N');

// Pied de page
$pdf->SetY(-35);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(128, 128, 128);
$pdf->Cell(0, 10, 'MarieTeam - Service de traversées maritimes', 0, 1, 'C');
$pdf->Cell(0, 10, 'Pour toute question, contactez-nous au +33 1 XX XX XX XX ou à contact@marieteam.fr', 0, 1, 'C');

// Fermer et générer le PDF
$pdf->Output('billet_marieteam_' . $numBillet . '.pdf', 'D');