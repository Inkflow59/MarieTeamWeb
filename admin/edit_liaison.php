<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Récupérer le code de la liaison à modifier
$code = $_GET['code'] ?? 0;
if (!$code) {
    $_SESSION['error_message'] = "Code de liaison non spécifié.";
    header('Location: dashboard.php');
    exit;
}

// Récupérer les informations de la liaison
$sqlLiaison = "SELECT l.code, l.distance, l.idSecteur, l.idPort_Depart, l.idPort_Arrivee, l.tempsLiaison,
               p1.nomPort AS portDepart, p2.nomPort AS portArrivee, s.nomSecteur
               FROM liaison l
               JOIN port p1 ON l.idPort_Depart = p1.idPort
               JOIN port p2 ON l.idPort_Arrivee = p2.idPort
               JOIN secteur s ON l.idSecteur = s.idSecteur
               WHERE l.code = ?";
$stmt = $db->prepare($sqlLiaison);
$stmt->bind_param("i", $code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Liaison introuvable.";
    header('Location: dashboard.php');
    exit;
}

$liaison = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier liaison - MarieTeam</title>
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
            <a href="manage_routes.php" class="menu-item active">Gérer les liaisons</a>
            <a href="manage_crossings.php" class="menu-item">Gérer les traversées</a>
            <a href="view_reservations.php" class="menu-item">Voir les réservations</a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Modifier la liaison #<?php echo $liaison['code']; ?></h2>
                <a href="dashboard.php" class="btn">Retour</a>
            </div>
            
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error_message']; 
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <form action="process_liaison.php" method="post">
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="code" value="<?php echo $liaison['code']; ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="port_depart">Port de départ</label>
                            <select id="port_depart" name="port_depart" class="form-control" required>
                                <?php
                                $sqlPorts = "SELECT idPort, nomPort FROM port ORDER BY nomPort";
                                $resultPorts = $db->query($sqlPorts);
                                while ($port = $resultPorts->fetch_assoc()) {
                                    $selected = ($port['idPort'] == $liaison['idPort_Depart']) ? 'selected' : '';
                                    echo '<option value="' . $port['idPort'] . '" ' . $selected . '>' . htmlspecialchars($port['nomPort']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="port_arrivee">Port d'arrivée</label>
                            <select id="port_arrivee" name="port_arrivee" class="form-control" required>
                                <?php
                                $resultPorts->data_seek(0);
                                while ($port = $resultPorts->fetch_assoc()) {
                                    $selected = ($port['idPort'] == $liaison['idPort_Arrivee']) ? 'selected' : '';
                                    echo '<option value="' . $port['idPort'] . '" ' . $selected . '>' . htmlspecialchars($port['nomPort']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="secteur">Secteur</label>
                            <select id="secteur" name="secteur" class="form-control" required>
                                <?php
                                $sqlSecteurs = "SELECT idSecteur, nomSecteur FROM secteur ORDER BY nomSecteur";
                                $resultSecteurs = $db->query($sqlSecteurs);
                                while ($secteur = $resultSecteurs->fetch_assoc()) {
                                    $selected = ($secteur['idSecteur'] == $liaison['idSecteur']) ? 'selected' : '';
                                    echo '<option value="' . $secteur['idSecteur'] . '" ' . $selected . '>' . htmlspecialchars($secteur['nomSecteur']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="distance">Distance (miles nautiques)</label>
                            <input type="number" id="distance" name="distance" class="form-control" step="0.1" min="0.1" value="<?php echo $liaison['distance']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="temps_liaison">Temps de liaison (hh:mm:ss)</label>
                            <input type="time" id="temps_liaison" name="temps_liaison" class="form-control" step="1" value="<?php echo substr($liaison['tempsLiaison'], 0, 5); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        <a href="dashboard.php" class="btn">Annuler</a>
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
