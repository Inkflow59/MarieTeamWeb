<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Filtrer par traversée si spécifié
$filtreTraversee = isset($_GET['traversee']) ? intval($_GET['traversee']) : 0;
$whereClause = $filtreTraversee > 0 ? "WHERE r.numTra = $filtreTraversee" : "";

// Récupérer la liste des réservations
$sqlReservations = "SELECT r.numRes, r.nomRes, r.adresse, r.codePostal, r.ville, t.date, t.heure, 
                   p1.nomPort as portDepart, p2.nomPort as portArrivee, b.nomBat,
                   GROUP_CONCAT(CONCAT(ty.libelleType, ' (', e.quantite, ')') SEPARATOR ', ') as passagers
                   FROM reservation r
                   JOIN traversee t ON r.numTra = t.numTra
                   JOIN liaison l ON t.code = l.code
                   JOIN port p1 ON l.idPort_Depart = p1.idPort
                   JOIN port p2 ON l.idPort_Arrivee = p2.idPort
                   JOIN bateau b ON t.idBat = b.idBat
                   JOIN enregistrer e ON r.numRes = e.numRes
                   JOIN type ty ON e.idType = ty.idType
                   $whereClause
                   GROUP BY r.numRes
                   ORDER BY t.date DESC, t.heure, r.numRes DESC";
$resultReservations = $db->query($sqlReservations);
$reservations = [];

if ($resultReservations) {
    while ($row = $resultReservations->fetch_assoc()) {
        $reservations[] = $row;
    }
}

// Récupérer la liste des traversées pour le filtre
$sqlTraversees = "SELECT t.numTra, t.date, t.heure, p1.nomPort as portDepart, p2.nomPort as portArrivee
                FROM traversee t
                JOIN liaison l ON t.code = l.code
                JOIN port p1 ON l.idPort_Depart = p1.idPort
                JOIN port p2 ON l.idPort_Arrivee = p2.idPort
                ORDER BY t.date DESC, t.heure";
$resultTraversees = $db->query($sqlTraversees);
$traversees = [];

if ($resultTraversees) {
    while ($row = $resultTraversees->fetch_assoc()) {
        $traversees[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des réservations - MarieTeam</title>
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
            <a href="manage_crossings.php" class="menu-item">Gérer les traversées</a>
            <a href="view_reservations.php" class="menu-item active">Voir les réservations</a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Liste des réservations</h2>
                
                <!-- Filtre par traversée -->
                <div class="filter-container">
                    <form action="view_reservations.php" method="get" class="filter-form">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label for="traversee">Filtrer par traversée:</label>
                            <select id="traversee" name="traversee" class="form-control" onchange="this.form.submit()">
                                <option value="0">Toutes les traversées</option>
                                <?php foreach ($traversees as $traversee): ?>
                                    <option value="<?php echo $traversee['numTra']; ?>" <?php echo ($filtreTraversee == $traversee['numTra']) ? 'selected' : ''; ?>>
                                        <?php echo date('d/m/Y', strtotime($traversee['date'])) . ' ' . substr($traversee['heure'], 0, 5) . ' - ' . $traversee['portDepart'] . ' → ' . $traversee['portArrivee']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Liste des réservations -->
            <table class="table">
                <thead>
                    <tr>
                        <th>N° Rés.</th>
                        <th>Client</th>
                        <th>Traversée</th>
                        <th>Passagers</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reservations)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">Aucune réservation trouvée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?php echo $reservation['numRes']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($reservation['nomRes']); ?></strong><br>
                                    <?php echo htmlspecialchars($reservation['adresse']); ?><br>
                                    <?php echo htmlspecialchars($reservation['codePostal'] . ' ' . $reservation['ville']); ?>
                                </td>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($reservation['date'])) . ' à ' . substr($reservation['heure'], 0, 5); ?></strong><br>
                                    <?php echo htmlspecialchars($reservation['portDepart'] . ' → ' . $reservation['portArrivee']); ?><br>
                                    Bateau: <?php echo htmlspecialchars($reservation['nomBat']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($reservation['passagers']); ?></td>
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
</body>
</html>
