<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Calculer les statistiques pour la période sélectionnée
$dateDebut = isset($_POST['date_debut']) ? $_POST['date_debut'] : date('Y-m-d', strtotime('-30 days'));
$dateFin = isset($_POST['date_fin']) ? $_POST['date_fin'] : date('Y-m-d');

// Calcul du chiffre d'affaires pour la période
$sqlCA = "SELECT SUM(tarif * e.quantite) as chiffreAffaires
          FROM reservation r
          JOIN traversee t ON r.numTra = t.numTra
          JOIN enregistrer e ON r.numRes = e.numRes
          JOIN tarifer tf ON e.idType = tf.idType AND t.code = tf.code
          WHERE t.date BETWEEN ? AND ?";
$stmtCA = $db->prepare($sqlCA);
$stmtCA->bind_param("ss", $dateDebut, $dateFin);
$stmtCA->execute();
$resultCA = $stmtCA->get_result();
$chiffreAffaires = $resultCA->fetch_assoc()['chiffreAffaires'] ?? 0;

// Nombre total de passagers pour la période
$sqlPassagers = "SELECT SUM(e.quantite) as totalPassagers
                FROM reservation r
                JOIN traversee t ON r.numTra = t.numTra
                JOIN enregistrer e ON r.numRes = e.numRes
                WHERE t.date BETWEEN ? AND ?";
$stmtPassagers = $db->prepare($sqlPassagers);
$stmtPassagers->bind_param("ss", $dateDebut, $dateFin);
$stmtPassagers->execute();
$resultPassagers = $stmtPassagers->get_result();
$totalPassagers = $resultPassagers->fetch_assoc()['totalPassagers'] ?? 0;

// Nombre de passagers par catégorie
$sqlCat = "SELECT ty.libelleType, SUM(e.quantite) as nombre
          FROM reservation r
          JOIN traversee t ON r.numTra = t.numTra
          JOIN enregistrer e ON r.numRes = e.numRes
          JOIN type ty ON e.idType = ty.idType
          WHERE t.date BETWEEN ? AND ?
          GROUP BY ty.libelleType
          ORDER BY nombre DESC";
$stmtCat = $db->prepare($sqlCat);
$stmtCat->bind_param("ss", $dateDebut, $dateFin);
$stmtCat->execute();
$resultCat = $stmtCat->get_result();
$passagersParCategorie = [];
while ($row = $resultCat->fetch_assoc()) {
    $passagersParCategorie[] = $row;
}

// Nombre de bateaux
$sqlBateaux = "SELECT COUNT(*) as nbBateaux FROM bateau";
$resultBateaux = $db->query($sqlBateaux);
$nbBateaux = $resultBateaux ? $resultBateaux->fetch_assoc()['nbBateaux'] : 0;

// Dernières réservations
$sqlDernieresReservations = "SELECT r.numRes, r.nomRes, r.ville, t.date, t.heure, b.nomBat
                            FROM reservation r
                            JOIN traversee t ON r.numTra = t.numTra
                            JOIN bateau b ON t.idBat = b.idBat
                            ORDER BY r.numRes DESC
                            LIMIT 5";
$resultDernieresReservations = $db->query($sqlDernieresReservations);
$dernieresReservations = [];
if ($resultDernieresReservations) {
    while ($row = $resultDernieresReservations->fetch_assoc()) {
        $dernieresReservations[] = $row;
    }
}

// Nombre de traversées prévues dans les 30 prochains jours
$sqlNbTraversees = "SELECT COUNT(*) as nbTraversees 
                   FROM traversee 
                   WHERE date >= CURRENT_DATE AND date <= DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY)";
$resultNbTraversees = $db->query($sqlNbTraversees);
$nbTraversees = $resultNbTraversees ? $resultNbTraversees->fetch_assoc()['nbTraversees'] : 0;

// Prochaines traversées
$sqlProchainesTraversees = "SELECT t.numTra, t.date, t.heure, b.nomBat, p1.nomPort as portDepart, p2.nomPort as portArrivee
                           FROM traversee t
                           JOIN liaison l ON t.code = l.code
                           JOIN port p1 ON l.idPort_Depart = p1.idPort
                           JOIN port p2 ON l.idPort_Arrivee = p2.idPort
                           JOIN bateau b ON t.idBat = b.idBat
                           WHERE t.date >= CURRENT_DATE
                           ORDER BY t.date, t.heure
                           LIMIT 5";
$resultProchainesTraversees = $db->query($sqlProchainesTraversees);
$prochainesTraversees = [];
if ($resultProchainesTraversees) {
    while ($row = $resultProchainesTraversees->fetch_assoc()) {
        $prochainesTraversees[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - MarieTeam</title>
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
            <a href="dashboard.php" class="menu-item active">Tableau de bord</a>
            <a href="add_admin.php" class="menu-item">Ajouter un administrateur</a>
            <a href="manage_boats.php" class="menu-item">Gérer les bateaux</a>
            <a href="manage_routes.php" class="menu-item">Gérer les liaisons</a>
            <a href="manage_crossings.php" class="menu-item">Gérer les traversées</a>
            <a href="view_reservations.php" class="menu-item">Voir les réservations</a>
        </div>
        
        <!-- Formulaire de filtrage par période -->
        <div class="card mb-4">
            <div class="card-header">
                <h2 class="card-title">Filtrer les statistiques par période</h2>
            </div>
            <div class="card-body">
                <form method="post" action="" class="filter-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut">Date de début</label>
                            <input type="date" id="date_debut" name="date_debut" class="form-control" value="<?php echo $dateDebut; ?>">
                        </div>
                        <div class="form-group">
                            <label for="date_fin">Date de fin</label>
                            <input type="date" id="date_fin" name="date_fin" class="form-control" value="<?php echo $dateFin; ?>">
                        </div>
                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="btn btn-primary">Appliquer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
          <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($totalPassagers); ?></div>
                <div class="stat-label">Passagers transportés<br><small>(période sélectionnée)</small></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($nbTraversees); ?></div>
                <div class="stat-label">Traversées (30 prochains jours)</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($nbBateaux); ?></div>
                <div class="stat-label">Bateaux</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-value"><?php echo number_format($chiffreAffaires, 2); ?> €</div>
                <div class="stat-label">Chiffre d'affaires<br><small>(période sélectionnée)</small></div>
            </div>
        </div>
        
        <!-- Passagers par catégorie -->
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Passagers par catégorie (<?php echo date('d/m/Y', strtotime($dateDebut)); ?> au <?php echo date('d/m/Y', strtotime($dateFin)); ?>)</h2>
                </div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Nombre</th>
                            <th>Pourcentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($passagersParCategorie)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center;">Aucune donnée disponible pour cette période.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($passagersParCategorie as $categorie): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($categorie['libelleType']); ?></td>
                                    <td><?php echo number_format($categorie['nombre']); ?></td>
                                    <td><?php echo number_format(($categorie['nombre'] / $totalPassagers) * 100, 1); ?> %</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Gestion des liaisons -->
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Gestion des liaisons</h2>
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
                                <label for="temps_liaison">Temps de liaison (hh:mm:ss)</label>
                                <input type="time" id="temps_liaison" name="temps_liaison" class="form-control" step="1" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                            <button type="button" class="btn" id="btnAnnulerAjout">Annuler</button>
                        </div>
                    </form>
                </div>
                
                <!-- Liste des liaisons existantes -->
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Port départ</th>
                            <th>Port arrivée</th>
                            <th>Secteur</th>
                            <th>Distance</th>
                            <th>Temps</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sqlLiaisons = "SELECT l.code, p1.nomPort AS portDepart, p2.nomPort AS portArrivee, 
                                       s.nomSecteur, l.distance, l.tempsLiaison
                                FROM liaison l
                                JOIN port p1 ON l.idPort_Depart = p1.idPort
                                JOIN port p2 ON l.idPort_Arrivee = p2.idPort
                                JOIN secteur s ON l.idSecteur = s.idSecteur
                                ORDER BY l.code";
                        $resultLiaisons = $db->query($sqlLiaisons);
                        
                        if ($resultLiaisons && $resultLiaisons->num_rows > 0) {
                            while ($liaison = $resultLiaisons->fetch_assoc()) {
                                ?>
                                <tr>
                                    <td><?php echo $liaison['code']; ?></td>
                                    <td><?php echo htmlspecialchars($liaison['portDepart']); ?></td>
                                    <td><?php echo htmlspecialchars($liaison['portArrivee']); ?></td>
                                    <td><?php echo htmlspecialchars($liaison['nomSecteur']); ?></td>
                                    <td><?php echo $liaison['distance']; ?> miles</td>
                                    <td><?php echo $liaison['tempsLiaison']; ?></td>
                                    <td>
                                        <a href="edit_liaison.php?code=<?php echo $liaison['code']; ?>" class="btn action-btn btn-edit">Modifier</a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Aucune liaison configurée.</td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="row">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Prochaines traversées</h2>
                    <a href="manage_crossings.php" class="btn">Gérer</a>
                </div>
                
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Traversée</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Bateau</th>
                            <th>Départ</th>
                            <th>Arrivée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($prochainesTraversees)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Aucune traversée prévue.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($prochainesTraversees as $traversee): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($traversee['numTra']); ?></td>
                                    <td><?php echo htmlspecialchars($traversee['date']); ?></td>
                                    <td><?php echo htmlspecialchars($traversee['heure']); ?></td>
                                    <td><?php echo htmlspecialchars($traversee['nomBat']); ?></td>
                                    <td><?php echo htmlspecialchars($traversee['portDepart']); ?></td>
                                    <td><?php echo htmlspecialchars($traversee['portArrivee']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
          <div class="dashboard-footer">
            <p>&copy; <?php echo date('Y'); ?> MarieTeam - Système d'Administration</p>
        </div>
    </div>
    
    <script>
        // Fonction pour afficher/masquer le formulaire d'ajout de liaison
        document.addEventListener('DOMContentLoaded', function() {
            const btnAjouter = document.getElementById('btnAjouterLiaison');
            const btnAnnuler = document.getElementById('btnAnnulerAjout');
            const formAjout = document.getElementById('formAjoutLiaison');
            
            btnAjouter.addEventListener('click', function() {
                formAjout.style.display = 'block';
                btnAjouter.style.display = 'none';
            });
            
            btnAnnuler.addEventListener('click', function() {
                formAjout.style.display = 'none';
                btnAjouter.style.display = 'inline-block';
            });
            
            // Vérifier que les ports sont différents
            const portDepart = document.getElementById('port_depart');
            const portArrivee = document.getElementById('port_arrivee');
            
            function checkPorts() {
                if (portDepart.value && portArrivee.value && portDepart.value === portArrivee.value) {
                    alert('Le port de départ et le port d\'arrivée doivent être différents.');
                    portArrivee.value = '';
                }
            }
            
            portDepart.addEventListener('change', checkPorts);
            portArrivee.addEventListener('change', checkPorts);
        });
    </script>
</body>
</html>
