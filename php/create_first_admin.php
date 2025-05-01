<?php
// Ce script sert à créer le premier administrateur. 
// Il doit être exécuté manuellement et sécurisé ou supprimé après utilisation.

// Connexion à la base de données
$db = new mysqli('localhost', 'root', '', 'marieteam');

// Vérifier la connexion
if ($db->connect_error) {
    die("Erreur de connexion à la base de données: " . $db->connect_error);
}

// Informations de l'administrateur par défaut
$nomUtilisateur = 'admin';
$mdp = 'admin123'; // À changer après la première connexion

// Vérifier si la table est vide
$query = "SELECT COUNT(*) as count FROM admin";
$result = $db->query($query);
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    echo "Des administrateurs existent déjà dans la base de données.<br>";
    echo "Si vous souhaitez réinitialiser, videz d'abord la table admin.<br>";
    echo "<a href='../admin/login.php'>Aller à la page de connexion</a>";
} else {
    // Hachez le mot de passe avant de l'insérer dans la base de données
    $mdp_hashed = password_hash($mdp, PASSWORD_DEFAULT);
    
    // Insérer l'administrateur avec le mot de passe haché
    $sql = "INSERT INTO admin (nomUtilisateur, mdp, lastLogin) VALUES (?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $nomUtilisateur, $mdp_hashed);
    if ($stmt->execute()) {
        echo "Administrateur par défaut créé avec succès!<br>";
        echo "Nom d'utilisateur: " . $nomUtilisateur . "<br>";
        echo "Mot de passe: " . $mdp . "<br>";
        echo "<strong>Important:</strong> Changez ce mot de passe dès que possible!<br>";
        echo "<a href='../admin/login.php'>Aller à la page de connexion</a>";
    } else {
        echo "Erreur lors de la création de l'administrateur: " . $stmt->error;
    }
    
    $stmt->close();
}

$db->close();
?>
