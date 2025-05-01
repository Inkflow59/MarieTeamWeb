<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

$error = '';

// Traitement du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Vérifier si les champs ne sont pas vides
    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {        // Requête pour vérifier les identifiants
        global $db;
        $stmt = $db->prepare("SELECT idAdmin, nomUtilisateur, mdp FROM admin WHERE nomUtilisateur = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Vérifier le mot de passe en utilisant password_verify
            if (password_verify($password, $admin['mdp'])) {
                // Connexion réussie, mettre à jour la date de dernière connexion
                $updateStmt = $db->prepare("UPDATE admin SET lastLogin = NOW() WHERE idAdmin = ?");
                $updateStmt->bind_param("i", $admin['idAdmin']);
                $updateStmt->execute();
                
                // Créer une session pour l'administrateur
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['idAdmin'];
                $_SESSION['admin_username'] = $admin['nomUtilisateur'];
                
                // Rediriger vers le tableau de bord
                header('Location: dashboard.php');
                exit;
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Nom d'utilisateur inconnu.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration - MarieTeam</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h1>Administration MarieTeam</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                </div>
            </form>
            
            <p style="text-align: center; margin-top: 20px;">
                <a href="../index.php">Retour au site</a>
            </p>
        </div>
    </div>
</body>
</html>
