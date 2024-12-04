<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />

  <link rel="stylesheet" href="css/navbar.css">

  <link rel="stylesheet" href="css/reservation.css">

  <title>Document</title>
</head>

<body>
  <header>
    <nav>
      <div class="left">
        <li class="signature">MarieTeam</li>
      </div>
      <div class="menu">
        <a href="index.html">
          <li>Accueil</li>
        </a>
        <a href="reservation.html">
          <li>Reservation</li>
        </a>
        <a href="#">
          <li>Contact</li>
        </a>
        <a href="#">
          <li>Mon ticket</li>
        </a>
      </div>
    </nav>
  </header>
  </head>

  <?php
include "php/BackCore.php";
?>

  <form method="GET" class="search-bar">
    <!-- Provenance Section -->
    <div class="section">
      <label for="provenance">D'où venez-vous ?</label>
      <select id="provenance" name="provenance">
        <option value="">Sélectionnez un secteur</option>
        <option value="Méditerrannée">Méditerrannée</option>
        <option value="Atlantique">Atlantique</option>
        <option value="Manche">Manche</option>
        <option value="Corse">Corse</option>
      </select>
    </div>

    <div class="divider"></div>

    <div class="divider"></div>

    <div class="section">
      <label for="destination">Départ</label>
      <input type="text" id="destination" name="destination" placeholder="Votre Départ" required>
    </div>

    <div class="divider"></div>


    <div class="section">
      <label for="arrivée">Arrivée</label>
      <input type="text" id="arrive" name="arrive" placeholder="Votre destination" required>
    </div>

    <div class="divider"></div>

    <div class="section">
      <label for="arrival-date">Date</label>

      <div class="relative max-w-sm">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
          <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            fill="currentColor" viewBox="0 0 20 20">
            <path
              d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
          </svg>
        </div>
        <input datepicker id="arrival_date" name="arrival_date" type="text"
          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
          placeholder="Select date">
      </div>
    </div>

    <button type="submit" class="search-button">
      <p>reserver</p>
    </button>
  </form>
  <?php
  $traversees = barreRecherche($_GET["provenance"], $_GET["arrival_date"], $_GET["destination"]);
  foreach($traversees as $t){
    echo "<div class='trajets'>

  <div class='center'>
  <div class='rect'>
    <svg xmlns='http://www.w3.org/2000/svg' width='60' height='61' viewBox='0 0 60 61' fill='none'>
      <path
        d='M10 44.25L7.5 30.5L30 23L52.5 30.5L50 44.25M12.5 28.8332V18C12.5 15.2386 14.7386 13 17.5 13H42.5C45.2615 13 47.5 15.2386 47.5 18V28.8332M25 13V8C25 6.6193 26.1193 5.5 27.5 5.5H32.5C33.8807 5.5 35 6.6193 35 8V13M5 53C7.5 55.5 15 55.5 20 50.5C25 55.5 35 55.5 40 50.5C45 55.5 52.5 55.5 55 53'
        stroke='black' stroke-width='5' stroke-linecap='round' stroke-linejoin='round' />
    </svg>
    <hr class='separation' />

    <div class='horaire'>
    <div class='horizontale'>
    <div class='verticale'>
      <p style='font-weight: 600;'>".substr($t['heure'],0, 5)."</p>
      <p style='font-style: italic;'>".$t["port_depart"]."</p>
    </div>
      <p class='separations'>-------</p>
      <div class='verticale'>
      <p style='font-weight: 600;'>".getHeureArrivee($t['numTra'])."</p>
      <p style='font-style: italic;'>".$t["port_arrivee"]."</p>
      </div>
    </div>
  </div>
    <button>Suivant</button>
  </div>
</div>";
  }
  ?>

  <script src="js/localisation.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>
</html>