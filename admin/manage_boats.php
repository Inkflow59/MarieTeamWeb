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

// Récupérer la liste des bateaux
$sqlBateaux = "SELECT idBat, nomBat, lienImage, Equipements FROM bateau ORDER BY nomBat";
$resultBateaux = $db->query($sqlBateaux);
$bateaux = [];
if ($resultBateaux) {
    while ($row = $resultBateaux->fetch_assoc()) {
        $bateaux[] = $row;
    }
}

// Traitement du formulaire d'ajout de bateau
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter') {
    $nomBat = $_POST['nom_bateau'] ?? '';
    $lienImage = $_POST['lien_image'] ?? '';
    $equipements = $_POST['equipements'] ?? '';
    
    if (empty($nomBat)) {
        $error = "Le nom du bateau est obligatoire.";
    } else {
        // Insérer le nouveau bateau
        $stmt = $db->prepare("INSERT INTO bateau (nomBat, lienImage, Equipements) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nomBat, $lienImage, $equipements);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Le bateau a été ajouté avec succès.";
            header('Location: manage_boats.php');
            exit;
        } else {
            $error = "Erreur lors de l'ajout du bateau : " . $db->error;
        }
    }
}

// Traitement de la suppression d'un bateau
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Vérifier si le bateau est utilisé dans des traversées
    $checkTraversees = $db->prepare("SELECT COUNT(*) AS count FROM traversee WHERE idBat = ?");
    $checkTraversees->bind_param("i", $id);
    $checkTraversees->execute();
    $traverseeCount = $checkTraversees->get_result()->fetch_assoc()['count'];
    
    if ($traverseeCount > 0) {
        $_SESSION['error_message'] = "Ce bateau ne peut pas être supprimé car il est utilisé dans des traversées.";
        header('Location: manage_boats.php');
        exit;
    }
    
    // Supprimer les capacités associées au bateau
    $deleteCapacites = $db->prepare("DELETE FROM contenir WHERE idBat = ?");
    $deleteCapacites->bind_param("i", $id);
    $deleteCapacites->execute();
    
    // Supprimer le bateau
    $deleteBoat = $db->prepare("DELETE FROM bateau WHERE idBat = ?");
    $deleteBoat->bind_param("i", $id);
    
    if ($deleteBoat->execute()) {
        $_SESSION['success_message'] = "Le bateau a été supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du bateau : " . $db->error;
    }
    
    header('Location: manage_boats.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des bateaux - MarieTeam</title>
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
            <a href="manage_boats.php" class="menu-item active">Gérer les bateaux</a>
            <a href="manage_routes.php" class="menu-item">Gérer les liaisons</a>
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
                <h2 class="card-title">Liste des bateaux</h2>
                <button class="btn" id="btnAjouterBateau">Ajouter un bateau</button>
            </div>
            
            <!-- Formulaire d'ajout de bateau -->
            <div id="formAjoutBateau" style="display: none; padding: 20px; background-color: #f8f9fa; margin-bottom: 20px; border-radius: 4px;">
                <h3>Ajouter un nouveau bateau</h3>
                <form action="manage_boats.php" method="post">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="nom_bateau">Nom du bateau</label>
                            <input type="text" id="nom_bateau" name="nom_bateau" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="lien_image">Lien vers l'image</label>
                            <input type="text" id="lien_image" name="lien_image" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="equipements">Équipements</label>
                            <textarea id="equipements" name="equipements" class="form-control" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <button type="button" class="btn" id="btnAnnulerAjout">Annuler</button>
                    </div>
                </form>
            </div>
            
            <!-- Liste des bateaux -->
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Image</th>
                        <th>Équipements</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bateaux)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">Aucun bateau enregistré.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bateaux as $bateau): ?>
                            <tr>
                                <td><?php echo $bateau['idBat']; ?></td>
                                <td><?php echo htmlspecialchars($bateau['nomBat']); ?></td>
                                <td>
                                    <?php if (!empty($bateau['lienImage'])): ?>
                                        <img src="<?php echo htmlspecialchars($bateau['lienImage']); ?>" alt="<?php echo htmlspecialchars($bateau['nomBat']); ?>" style="max-width: 100px; max-height: 60px;">
                                    <?php else: ?>
                                        <span>Pas d'image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($bateau['Equipements'])); ?></td>
                                <td>
                                    <a href="edit_bateau.php?id=<?php echo $bateau['idBat']; ?>" class="btn btn-small">Modifier</a>
                                    <a href="manage_boats.php?action=supprimer&id=<?php echo $bateau['idBat']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bateau ?')">Supprimer</a>
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
            const btnAjouterBateau = document.getElementById('btnAjouterBateau');
            const btnAnnulerAjout = document.getElementById('btnAnnulerAjout');
            const formAjoutBateau = document.getElementById('formAjoutBateau');
            
            btnAjouterBateau.addEventListener('click', function() {
                formAjoutBateau.style.display = 'block';
                btnAjouterBateau.style.display = 'none';
            });
            
            btnAnnulerAjout.addEventListener('click', function() {
                formAjoutBateau.style.display = 'none';
                btnAjouterBateau.style.display = 'inline-block';
            });
        });
    </script>
</body>
</html>
