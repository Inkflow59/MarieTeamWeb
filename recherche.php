<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/recherche.css">
    <title>MarieTeam - Accueil</title>
</head>
<body>
    <?php include 'module/header.php'; ?>
        <form action="php/recherche" method="post">
        <h1 class="titre">Rechercher votre billets</h1>
            <input type="text" class="ecrire" name="billet" id="billet">
            <input type="submit" class="envoyer" value="recherche">
        </form>
    </div>
</body>
</html>