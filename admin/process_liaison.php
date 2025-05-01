<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Vérifier la connexion
if ($db->connect_error) {
    die("Erreur de connexion: " . $db->connect_error);
}

// Récupérer l'action à effectuer
$action = $_POST['action'] ?? '';

// Traitement selon l'action
switch ($action) {
    case 'ajouter':
        // Récupérer les données du formulaire
        $port_depart = $_POST['port_depart'] ?? '';
        $port_arrivee = $_POST['port_arrivee'] ?? '';
        $secteur = $_POST['secteur'] ?? '';
        $distance = $_POST['distance'] ?? '';
        $temps_liaison = $_POST['temps_liaison'] ?? '';
        
        // Vérifier que tous les champs sont remplis
        if (empty($port_depart) || empty($port_arrivee) || empty($secteur) || empty($distance) || empty($temps_liaison)) {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            header('Location: dashboard.php');
            exit;
        }
        
        // Vérifier que les ports de départ et d'arrivée sont différents
        if ($port_depart == $port_arrivee) {
            $_SESSION['error_message'] = "Les ports de départ et d'arrivée doivent être différents.";
            header('Location: dashboard.php');
            exit;
        }
        
        // Formater le temps de liaison au format HH:MM:SS
        if (strpos($temps_liaison, ':') === false) {
            $temps_liaison = $temps_liaison . ':00';
        }
        
        // Insérer la nouvelle liaison
        $sql = "INSERT INTO liaison (distance, idSecteur, idPort_Depart, idPort_Arrivee, tempsLiaison) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("diiss", $distance, $secteur, $port_depart, $port_arrivee, $temps_liaison);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Liaison ajoutée avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de l'ajout de la liaison: " . $db->error;
        }
        break;
        
    case 'modifier':
        // Récupérer les données du formulaire
        $code = $_POST['code'] ?? '';
        $port_depart = $_POST['port_depart'] ?? '';
        $port_arrivee = $_POST['port_arrivee'] ?? '';
        $secteur = $_POST['secteur'] ?? '';
        $distance = $_POST['distance'] ?? '';
        $temps_liaison = $_POST['temps_liaison'] ?? '';
        
        // Vérifier que tous les champs sont remplis
        if (empty($code) || empty($port_depart) || empty($port_arrivee) || empty($secteur) || empty($distance) || empty($temps_liaison)) {
            $_SESSION['error_message'] = "Tous les champs sont obligatoires.";
            header('Location: edit_liaison.php?code=' . $code);
            exit;
        }
        
        // Vérifier que les ports de départ et d'arrivée sont différents
        if ($port_depart == $port_arrivee) {
            $_SESSION['error_message'] = "Les ports de départ et d'arrivée doivent être différents.";
            header('Location: edit_liaison.php?code=' . $code);
            exit;
        }
        
        // Mettre à jour la liaison
        $sql = "UPDATE liaison 
                SET distance = ?, idSecteur = ?, idPort_Depart = ?, idPort_Arrivee = ?, tempsLiaison = ? 
                WHERE code = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("diissi", $distance, $secteur, $port_depart, $port_arrivee, $temps_liaison, $code);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Liaison modifiée avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la modification de la liaison: " . $db->error;
        }
        break;
    
    default:
        $_SESSION['error_message'] = "Action non reconnue.";
        break;
}

// Rediriger vers le tableau de bord
header('Location: dashboard.php');
exit;
