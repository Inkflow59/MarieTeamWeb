<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/recherche.css">
    <link rel="stylesheet" href="css/navbar.css">
    <title>MarieTeam - Accueil</title>
</head>
<body>
    <header>
        <nav>
            <div class="left">
                <li class="signature">MarieTeam</li>
            </div>
            <div class="menu">
                <a href="index.html"><li>Accueil</li></a>
                <a href="reservation.php"><li>Reservation</li></a>
                <a href="#"><li>Contact</li></a>
                <a href="#"><li>Mon ticket</li></a>
            </div>
        </nav>
    </header>
        <form action="php/recherche" method="post">
        <h1 class="titre">Rechercher votre billets</h1>
            <input type="text" class="ecrire" name="billet" id="billet">
            <input type="submit" class="envoyer" value="recherche">
        </form>
    </div>
</body>
</html>