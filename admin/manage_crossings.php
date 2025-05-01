<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Message de succès ou d'erreur
$message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Récupérer la liste des traversées
$sqlTraversees = "SELECT t.numTra, t.date, t.heure, l.code, b.idBat, b.nomBat, p1.nomPort as portDepart, p2.nomPort as portArrivee
                 FROM traversee t
                 JOIN liaison l ON t.code = l.code
                 JOIN port p1 ON l.idPort_Depart = p1.idPort
                 JOIN port p2 ON l.idPort_Arrivee = p2.idPort
                 JOIN bateau b ON t.idBat = b.idBat
                 ORDER BY t.date DESC, t.heure";
$resultTraversees = $db->query($sqlTraversees);
$traversees = [];
if ($resultTraversees) {
    while ($row = $resultTraversees->fetch_assoc()) {
        $traversees[] = $row;
    }
}

// Traitement de la suppression d'une traversée
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier si la traversée a des réservations
    $checkReservations = $db->prepare("SELECT COUNT(*) AS count FROM reservation WHERE numTra = ?");
    $checkReservations->bind_param("i", $id);
    $checkReservations->execute();
    $reservationCount = $checkReservations->get_result()->fetch_assoc()['count'];
    
    if ($reservationCount > 0) {
        $_SESSION['error_message'] = "Cette traversée ne peut pas être supprimée car elle comporte des réservations.";
        header('Location: manage_crossings.php');
        exit;
    }
    
    // Supprimer la traversée
    $deleteTraversee = $db->prepare("DELETE FROM traversee WHERE numTra = ?");
    $deleteTraversee->bind_param("i", $id);
    
    if ($deleteTraversee->execute()) {
        $_SESSION['success_message'] = "La traversée a été supprimée avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de la traversée : " . $db->error;
    }
    
    header('Location: manage_crossings.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des traversées - MarieTeam</title>
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
        
        <!-- Affichage des messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Liste des traversées</h2>
                <button class="btn" id="btnAjouterTraversee">Ajouter une traversée</button>
            </div>
            
            <!-- Formulaire d'ajout de traversée (caché par défaut) -->
            <div id="formAjoutTraversee" style="display: none; padding: 20px; background-color: #f8f9fa; margin-bottom: 20px; border-radius: 4px;">
                <h3>Ajouter une nouvelle traversée</h3>
                <form action="process_traversee.php" method="post">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="liaison">Liaison</label>
                            <select id="liaison" name="liaison" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <?php
                                $sqlLiaisons = "SELECT l.code, p1.nomPort as portDepart, p2.nomPort as portArrivee 
                                              FROM liaison l 
                                              JOIN port p1 ON l.idPort_Depart = p1.idPort 
                                              JOIN port p2 ON l.idPort_Arrivee = p2.idPort 
                                              ORDER BY portDepart, portArrivee";
                                $resultLiaisons = $db->query($sqlLiaisons);
                                while ($liaison = $resultLiaisons->fetch_assoc()) {
                                    echo '<option value="' . $liaison['code'] . '">' . htmlspecialchars($liaison['portDepart'] . ' - ' . $liaison['portArrivee']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bateau">Bateau</label>
                            <select id="bateau" name="bateau" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <?php
                                $sqlBateaux = "SELECT idBat, nomBat FROM bateau ORDER BY nomBat";
                                $resultBateaux = $db->query($sqlBateaux);
                                while ($bateau = $resultBateaux->fetch_assoc()) {
                                    echo '<option value="' . $bateau['idBat'] . '">' . htmlspecialchars($bateau['nomBat']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="heure">Heure</label>
                            <input type="time" id="heure" name="heure" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn" id="btnAnnulerAjout">Annuler</button>
                    </div>
                </form>
            </div>
            
            <!-- Liste des traversées -->
            <table class="table">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Liaison</th>
                        <th>Bateau</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($traversees)): ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">Aucune traversée enregistrée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($traversees as $traversee): ?>
                            <tr>
                                <td><?php echo $traversee['numTra']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($traversee['date'])); ?></td>
                                <td><?php echo date('H:i', strtotime($traversee['heure'])); ?></td>
                                <td><?php echo htmlspecialchars($traversee['portDepart'] . ' - ' . $traversee['portArrivee']); ?></td>
                                <td><?php echo htmlspecialchars($traversee['nomBat']); ?></td>
                                <td>
                                    <a href="edit_traversee.php?id=<?php echo $traversee['numTra']; ?>" class="btn btn-small">Modifier</a>
                                    <a href="manage_crossings.php?action=supprimer&id=<?php echo $traversee['numTra']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette traversée ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> MarieTeam - Système d'Administration</p>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnAjouterTraversee = document.getElementById('btnAjouterTraversee');
            const btnAnnulerAjout = document.getElementById('btnAnnulerAjout');
            const formAjoutTraversee = document.getElementById('formAjoutTraversee');
            
            btnAjouterTraversee.addEventListener('click', function() {
                formAjoutTraversee.style.display = 'block';
                btnAjouterTraversee.style.display = 'none';
            });
            
            btnAnnulerAjout.addEventListener('click', function() {
                formAjoutTraversee.style.display = 'none';
                btnAjouterTraversee.style.display = 'inline-block';
            });
        });
    </script>
</body>
</html>
