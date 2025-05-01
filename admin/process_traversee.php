<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'ajouter') {
        $liaison = $_POST['liaison'] ?? '';
        $bateau = $_POST['bateau'] ?? '';
        $date = $_POST['date'] ?? '';
        $heure = $_POST['heure'] ?? '';
        
        if (empty($liaison) || empty($bateau) || empty($date) || empty($heure)) {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            header('Location: manage_crossings.php');
            exit;
        }
        
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $_SESSION['error_message'] = "La date de traversée ne peut pas être antérieure à aujourd'hui.";
            header('Location: manage_crossings.php');
            exit;
        }
        
        $stmt = $db->prepare("INSERT INTO traversee (code, idBat, date, heure) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $liaison, $bateau, $date, $heure);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "La traversée a été ajoutée avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de l'ajout de la traversée : " . $db->error;
        }
        
        header('Location: manage_crossings.php');
        exit;
    }
}

$_SESSION['error_message'] = "Action non valide ou méthode non autorisée.";
header('Location: manage_crossings.php');
exit;
?>
