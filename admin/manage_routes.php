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

// Récupérer la liste des liaisons
$sqlLiaisons = "SELECT l.code, l.distance, l.tempsLiaison, p1.nomPort AS portDepart, p2.nomPort AS portArrivee, s.nomSecteur
               FROM liaison l
               JOIN port p1 ON l.idPort_Depart = p1.idPort
               JOIN port p2 ON l.idPort_Arrivee = p2.idPort
               JOIN secteur s ON l.idSecteur = s.idSecteur
               ORDER BY l.code";
$resultLiaisons = $db->query($sqlLiaisons);
$liaisons = [];
if ($resultLiaisons) {
    while ($row = $resultLiaisons->fetch_assoc()) {
        $liaisons[] = $row;
    }
}

// Traitement de la suppression d'une liaison
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['code'])) {
    $code = intval($_GET['code']);
    
    // Vérifier si la liaison est utilisée dans des traversées
    $checkTraversees = $db->prepare("SELECT COUNT(*) AS count FROM traversee WHERE code = ?");
    $checkTraversees->bind_param("i", $code);
    $checkTraversees->execute();
    $traverseeCount = $checkTraversees->get_result()->fetch_assoc()['count'];
    
    if ($traverseeCount > 0) {
        $_SESSION['error_message'] = "Cette liaison ne peut pas être supprimée car elle est utilisée dans des traversées.";
        header('Location: manage_routes.php');
        exit;
    }
    
    // Supprimer les tarifs associés à la liaison
    $deleteTarifs = $db->prepare("DELETE FROM tarifer WHERE code = ?");
    $deleteTarifs->bind_param("i", $code);
    $deleteTarifs->execute();
    
    // Supprimer la liaison
    $deleteLiaison = $db->prepare("DELETE FROM liaison WHERE code = ?");
    $deleteLiaison->bind_param("i", $code);
    
    if ($deleteLiaison->execute()) {
        $_SESSION['success_message'] = "La liaison a été supprimée avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression de la liaison : " . $db->error;
    }
    
    header('Location: manage_routes.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des liaisons - MarieTeam</title>
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
                <h2 class="card-title">Liste des liaisons</h2>
                <button class="btn" id="btnAjouterLiaison">Ajouter une liaison</button>
            </div>
            
            <!-- Formulaire d'ajout de liaison (caché par défaut) -->
            <div id="formAjoutLiaison" style="display: none; padding: 20px; background-color: #f8f9fa; margin-bottom: 20px; border-radius: 4px;">
                <h3>Ajouter une nouvelle liaison</h3>
                <form action="process_liaison.php" method="post">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="port_depart">Port de départ</label>
                            <select id="port_depart" name="port_depart" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <?php
                                $sqlPorts = "SELECT idPort, nomPort FROM port ORDER BY nomPort";
                                $resultPorts = $db->query($sqlPorts);
                                while ($port = $resultPorts->fetch_assoc()) {
                                    echo '<option value="' . $port['idPort'] . '">' . htmlspecialchars($port['nomPort']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="port_arrivee">Port d'arrivée</label>
                            <select id="port_arrivee" name="port_arrivee" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <?php
                                $resultPorts->data_seek(0);
                                while ($port = $resultPorts->fetch_assoc()) {
                                    echo '<option value="' . $port['idPort'] . '">' . htmlspecialchars($port['nomPort']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="secteur">Secteur</label>
                            <select id="secteur" name="secteur" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <?php
                                $sqlSecteurs = "SELECT idSecteur, nomSecteur FROM secteur ORDER BY nomSecteur";
                                $resultSecteurs = $db->query($sqlSecteurs);
                                while ($secteur = $resultSecteurs->fetch_assoc()) {
                                    echo '<option value="' . $secteur['idSecteur'] . '">' . htmlspecialchars($secteur['nomSecteur']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="distance">Distance (miles nautiques)</label>
                            <input type="number" id="distance" name="distance" class="form-control" step="0.1" min="0.1" required>
                        </div>
                        <div class="form-group">
                            <label for="temps_liaison">Temps de liaison (hh:mm)</label>
                            <input type="time" id="temps_liaison" name="temps_liaison" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn" id="btnAnnulerAjout">Annuler</button>
                    </div>
                </form>
            </div>
            
            <!-- Liste des liaisons -->
            <table class="table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Port de départ</th>
                        <th>Port d'arrivée</th>
                        <th>Secteur</th>
                        <th>Distance</th>
                        <th>Temps</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($liaisons)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">Aucune liaison enregistrée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($liaisons as $liaison): ?>
                            <tr>
                                <td><?php echo $liaison['code']; ?></td>
                                <td><?php echo htmlspecialchars($liaison['portDepart']); ?></td>
                                <td><?php echo htmlspecialchars($liaison['portArrivee']); ?></td>
                                <td><?php echo htmlspecialchars($liaison['nomSecteur']); ?></td>
                                <td><?php echo $liaison['distance']; ?> miles</td>
                                <td><?php echo $liaison['tempsLiaison']; ?></td>
                                <td>
                                    <a href="edit_liaison.php?code=<?php echo $liaison['code']; ?>" class="btn btn-small">Modifier</a>
                                    <a href="manage_routes.php?action=supprimer&code=<?php echo $liaison['code']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette liaison ?')">Supprimer</a>
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
            const btnAjouterLiaison = document.getElementById('btnAjouterLiaison');
            const btnAnnulerAjout = document.getElementById('btnAnnulerAjout');
            const formAjoutLiaison = document.getElementById('formAjoutLiaison');
            
            btnAjouterLiaison.addEventListener('click', function() {
                formAjoutLiaison.style.display = 'block';
                btnAjouterLiaison.style.display = 'none';
            });
            
            btnAnnulerAjout.addEventListener('click', function() {
                formAjoutLiaison.style.display = 'none';
                btnAjouterLiaison.style.display = 'inline-block';
            });
        });
    </script>
</body>
</html>
