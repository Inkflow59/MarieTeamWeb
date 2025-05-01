<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Récupérer l'ID de la traversée à modifier
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    $_SESSION['error_message'] = "ID de traversée non spécifié.";
    header('Location: manage_crossings.php');
    exit;
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $liaison = $_POST['liaison'] ?? '';
    $bateau = $_POST['bateau'] ?? '';
    $date = $_POST['date'] ?? '';
    $heure = $_POST['heure'] ?? '';
    
    if (empty($liaison) || empty($bateau) || empty($date) || empty($heure)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (strtotime($date) < strtotime(date('Y-m-d'))) {
        $error = "La date de traversée ne peut pas être antérieure à aujourd'hui.";
    } else {
        $stmt = $db->prepare("UPDATE traversee SET code = ?, idBat = ?, date = ?, heure = ? WHERE numTra = ?");
        $stmt->bind_param("iissi", $liaison, $bateau, $date, $heure, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "La traversée a été mise à jour avec succès.";
            header('Location: manage_crossings.php');
            exit;
        } else {
            $error = "Erreur lors de la mise à jour de la traversée : " . $db->error;
        }
    }
}

// Récupérer les informations actuelles de la traversée
$stmt = $db->prepare("SELECT t.numTra, t.date, t.heure, t.code, t.idBat, l.idPort_Depart, l.idPort_Arrivee, b.nomBat, 
                     p1.nomPort as portDepart, p2.nomPort as portArrivee
                     FROM traversee t
                     JOIN liaison l ON t.code = l.code
                     JOIN port p1 ON l.idPort_Depart = p1.idPort
                     JOIN port p2 ON l.idPort_Arrivee = p2.idPort
                     JOIN bateau b ON t.idBat = b.idBat
                     WHERE t.numTra = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Traversée introuvable.";
    header('Location: manage_crossings.php');
    exit;
}

$traversee = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une traversée - MarieTeam</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Administration MarieTeam</h1>
            <div>
                <span>Connecté en tant que <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="btn">Déconnexion</a>
            </div>
        </div>
        
        <div class="dashboard-menu">
            <a href="dashboard.php" class="menu-item">Tableau de bord</a>
            <a href="add_admin.php" class="menu-item">Ajouter un administrateur</a>
            <a href="manage_boats.php" class="menu-item">Gérer les bateaux</a>
            <a href="manage_routes.php" class="menu-item">Gérer les liaisons</a>
            <a href="manage_crossings.php" class="menu-item active">Gérer les traversées</a>
            <a href="view_reservations.php" class="menu-item">Voir les réservations</a>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Modifier la traversée #<?php echo $traversee['numTra']; ?></h2>
                <a href="manage_crossings.php" class="btn">Retour à la liste</a>
            </div>
            
            <div class="card-body">
                <form action="edit_traversee.php?id=<?php echo $id; ?>" method="post">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="liaison">Liaison</label>
                            <select id="liaison" name="liaison" class="form-control" required>
                                <?php
                                $sqlLiaisons = "SELECT l.code, p1.nomPort as portDepart, p2.nomPort as portArrivee 
                                              FROM liaison l 
                                              JOIN port p1 ON l.idPort_Depart = p1.idPort 
                                              JOIN port p2 ON l.idPort_Arrivee = p2.idPort 
                                              ORDER BY portDepart, portArrivee";
                                $resultLiaisons = $db->query($sqlLiaisons);
                                while ($liaison = $resultLiaisons->fetch_assoc()) {
                                    $selected = ($liaison['code'] == $traversee['code']) ? 'selected' : '';
                                    echo '<option value="' . $liaison['code'] . '" ' . $selected . '>' . htmlspecialchars($liaison['portDepart'] . ' - ' . $liaison['portArrivee']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bateau">Bateau</label>
                            <select id="bateau" name="bateau" class="form-control" required>
                                <?php
                                $sqlBateaux = "SELECT idBat, nomBat FROM bateau ORDER BY nomBat";
                                $resultBateaux = $db->query($sqlBateaux);
                                while ($bateau = $resultBateaux->fetch_assoc()) {
                                    $selected = ($bateau['idBat'] == $traversee['idBat']) ? 'selected' : '';
                                    echo '<option value="' . $bateau['idBat'] . '" ' . $selected . '>' . htmlspecialchars($bateau['nomBat']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $traversee['date']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="heure">Heure</label>
                            <input type="time" id="heure" name="heure" class="form-control" value="<?php echo substr($traversee['heure'], 0, 5); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        <a href="manage_crossings.php" class="btn">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> MarieTeam - Système d'Administration</p>
        </div>
    </div>
</body>
</html>
