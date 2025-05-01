<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Récupérer l'ID du bateau à modifier
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$id) {
    $_SESSION['error_message'] = "ID de bateau non spécifié.";
    header('Location: manage_boats.php');
    exit;
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomBat = $_POST['nom_bateau'] ?? '';
    $lienImage = $_POST['lien_image'] ?? '';
    $equipements = $_POST['equipements'] ?? '';
    
    if (empty($nomBat)) {
        $error = "Le nom du bateau est obligatoire.";
    } else {
        $stmt = $db->prepare("UPDATE bateau SET nomBat = ?, lienImage = ?, Equipements = ? WHERE idBat = ?");
        $stmt->bind_param("sssi", $nomBat, $lienImage, $equipements, $id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Les informations du bateau ont été mises à jour avec succès.";
            header('Location: manage_boats.php');
            exit;
        } else {
            $error = "Erreur lors de la mise à jour du bateau : " . $db->error;
        }
    }
}

// Récupérer les informations actuelles du bateau
$stmt = $db->prepare("SELECT idBat, nomBat, lienImage, Equipements FROM bateau WHERE idBat = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error_message'] = "Bateau introuvable.";
    header('Location: manage_boats.php');
    exit;
}

$bateau = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un bateau - MarieTeam</title>
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
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Modifier le bateau: <?php echo htmlspecialchars($bateau['nomBat']); ?></h2>
                <a href="manage_boats.php" class="btn">Retour à la liste</a>
            </div>
            
            <div class="card-body">
                <form action="edit_bateau.php?id=<?php echo $id; ?>" method="post">
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="nom_bateau">Nom du bateau</label>
                            <input type="text" id="nom_bateau" name="nom_bateau" class="form-control" value="<?php echo htmlspecialchars($bateau['nomBat']); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="lien_image">Lien vers l'image</label>
                            <input type="text" id="lien_image" name="lien_image" class="form-control" value="<?php echo htmlspecialchars($bateau['lienImage']); ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group form-group-full">
                            <label for="equipements">Équipements</label>
                            <textarea id="equipements" name="equipements" class="form-control" rows="4"><?php echo htmlspecialchars($bateau['Equipements']); ?></textarea>
                        </div>
                    </div>
                    
                    <?php if (!empty($bateau['lienImage'])): ?>
                        <div class="form-row">
                            <div class="form-group form-group-full">
                                <label>Aperçu de l'image actuelle</label>
                                <div>
                                    <img src="<?php echo htmlspecialchars($bateau['lienImage']); ?>" alt="<?php echo htmlspecialchars($bateau['nomBat']); ?>" style="max-width: 300px; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                        <a href="manage_boats.php" class="btn">Annuler</a>
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
