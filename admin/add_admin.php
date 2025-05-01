<?php
session_start();

// Vérifier si l'utilisateur est connecté en tant qu'administrateur
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

$message = '';
$error = '';

// Traitement du formulaire de création d'administrateur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['newUsername'] ?? '';
    $newPassword = $_POST['newPassword'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    // Vérifier si les champs ne sont pas vides
    if (empty($newUsername) || empty($newPassword) || empty($confirmPassword)) {
        $error = "Veuillez remplir tous les champs.";
    } 
    // Vérifier que les mots de passe correspondent
    elseif ($newPassword !== $confirmPassword) {
        $error = "Les mots de passe ne correspondent pas.";
    } 
    // Vérifier si le nom d'utilisateur existe déjà
    else {
        global $db;
        $stmt = $db->prepare("SELECT idAdmin FROM admin WHERE nomUtilisateur = ?");
        $stmt->bind_param("s", $newUsername);
        $stmt->execute();
        $result = $stmt->get_result();
          if ($result->num_rows > 0) {
            $error = "Ce nom d'utilisateur existe déjà.";
        } else {
            // Insérer le nouvel administrateur avec mot de passe haché
            $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);
            $insertStmt = $db->prepare("INSERT INTO admin (nomUtilisateur, mdp, lastLogin) VALUES (?, ?, NOW())");
            $insertStmt->bind_param("ss", $newUsername, $hashed_password);
            if ($insertStmt->execute()) {
                $message = "Nouvel administrateur créé avec succès!";
            } else {
                $error = "Erreur lors de la création de l'administrateur: " . $db->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un administrateur - MarieTeam</title>
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
            <a href="add_admin.php" class="menu-item active">Ajouter un administrateur</a>
            <a href="manage_boats.php" class="menu-item">Gérer les bateaux</a>
            <a href="manage_routes.php" class="menu-item">Gérer les liaisons</a>
            <a href="manage_crossings.php" class="menu-item">Gérer les traversées</a>
            <a href="view_reservations.php" class="menu-item">Voir les réservations</a>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Ajouter un nouvel administrateur</h2>
            </div>
            
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
            
            <form method="post" action="">
                <div class="form-row">
                    <div class="form-group form-group-full">
                        <label for="newUsername">Nom d'utilisateur</label>
                        <input type="text" id="newUsername" name="newUsername" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="newPassword">Mot de passe</label>
                        <input type="password" id="newPassword" name="newPassword" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirmer le mot de passe</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Créer l'administrateur</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
