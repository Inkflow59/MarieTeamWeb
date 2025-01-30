<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/navbar.css">

    <title>MarieTeam</title>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">MarieTeam</div>
            
            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <ul class="nav-menu">
                <li class="nav-item"><a href="index.php" class="nav-link">Accueil</a></li>
                <li class="nav-item"><a href="reservation.php" class="nav-link">Reservation</a></li>
                <li class="nav-item"><a href="recherche_billet" class="nav-link">Mon billet</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Support</a></li>
            </ul>

            <button id="theme-toggle" class="theme-toggle">
                <span id="theme-icon">🌙</span> <!-- Icône de lune -->
            </button>
        </nav>
    </header>

    <script>
        const hamburger = document.querySelector(".hamburger");
        const navMenu = document.querySelector(".nav-menu");

        hamburger.addEventListener("click", () => {
            hamburger.classList.toggle("active");
            navMenu.classList.toggle("active");
        });

        document.querySelectorAll(".nav-link").forEach(n => n.addEventListener("click", () => {
            hamburger.classList.remove("active");
            navMenu.classList.remove("active");
        }));
    </script>
</body>
</html>