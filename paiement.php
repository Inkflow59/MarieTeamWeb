<?php include("php/BackCore.php"); ?>
<?php
include 'module/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/payement.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet'>
    <script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <title>Paiement</title>
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

    <div class="bg"></div>

    <div class="container d-flex justify-content-center mt-5 mb-5">
        <div class="row g-3">
          <div class="col-md-6">  
            <span>Payment Method</span>
            <div class="card">
              <div class="accordion" id="accordionExample">
                <div class="card">
                  <div class="card-header p-0">
                    <h2 class="mb-0">
                      <button class="btn btn-light btn-block text-left p-3 rounded-0" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <div class="d-flex align-items-center justify-content-between">

                          <span>Carte de crédit</span>
                          <div class="icons">
                            <img src="img/mastercard.png" width="30">
                            <img src="img/visa.png" width="30">
                            <img src="img/stripe.png" width="30">
                            <img src="img/americain.png" width="30">
                          </div>
                          
                        </div>
                      </button>
                    </h2>
                  </div>
                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body payment-card-body">
                      <span class="font-weight-normal card-text">Numéro de carte</span>
                      <div class="input">
                        <i class="fa fa-credit-card"></i>
                        <input type="text" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19" oninput="formatInput(this)" onkeypress="return isNumberKey(event)">                      </div> 
                      <div class="row mt-3 mb-3">
                        <div class="col-md-6">
                          <span class="font-weight-normal card-text">Date d'expiration</span>
                          <div class="input">
                            <i class="fa fa-calendar"></i>
                            <input type="text" class="form-control" placeholder="MM/YY" maxlength="5" oninput="formatDateInput(this)" onkeypress="return isNumberKey(event)">                          
                          </div> 
                        </div>
                        <div class="col-md-6">
                          <span class="font-weight-normal card-text">CVC/CVV</span>
                          <div class="input">
                            <i class="fa fa-lock"></i>
                            <input type="text" class="form-control" placeholder="000" maxlength="3" onkeypress="return isNumberKey(event)">
                          </div> 
                        </div>
                      </div>
                      <span class="text-muted certificate-text"><i class="fa fa-lock"></i>Votre transaction est sécurisé avec le certificat SSL</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6">
              <span>Sommaire</span>
              <div class="card">
                <div class="d-flex justify-content-between p-3">
                  <div class="d-flex flex-column">
                    <span>Facturation Total</span>
                    <a href="#" class="billing">Save 20% with annual billing</a>
                  </div>
                  <div class="mt-1">
                    <sup class="super-price" id="totalPrice">NULL €</sup>
                  </div>
                </div>
                <hr class="mt-0 line">
                <div class="p-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span>Reservation en ligne : </span>
                    <span>5.00€</span>              
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>TVA : </span>
                    <span>20%</span>                   
                  </div>
                </div>
                <hr class="mt-0 line">
                <div class="p-3 d-flex justify-content-between">
                  <div class="d-flex flex-column">
                    <span>Montant total à payer</span>
                    <small></small>
                  </div>
                  <span id="totalWithTax">NULL€</span>
                </div>
                <div class="p-3">
                    <button class="btn btn-primary btn-block free-button" id="paymentButton">Paiement</button> 
                    <div class="text-center">
                        <a href="#">Nos conditions d'utilisations</a>
                    </div> 
                </div>
              </div>
          </div>  
        </div>
    </div>

    <script src="js/carte.js"></script>
    <script>
        // Récupérer les données de sessionStorage
        const reservationData = JSON.parse(sessionStorage.getItem('reservationData'));
        const prixTotal = parseFloat(reservationData.prixTotal) || 0;
        const tva = 0.20;
        const totalAvecTaxe = prixTotal + (prixTotal * tva);

        // Mettre à jour les éléments avec les valeurs calculées
        document.getElementById('totalPrice').textContent = prixTotal.toFixed(2) + ' €';
        document.getElementById('totalWithTax').textContent = totalAvecTaxe.toFixed(2) + ' €';

        document.getElementById('paymentButton').addEventListener('click', function() {
            const reservationData = JSON.parse(sessionStorage.getItem('reservationData'));
            
            // Créer un formulaire temporaire
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'php/reserver.php';

            // Ajouter les données en tant que champ caché
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'reservationData';
            input.value = JSON.stringify(reservationData);
            form.appendChild(input);

            // Ajouter le formulaire au document et le soumettre
            document.body.appendChild(form);
            form.submit();
        });
    </script>
<?php
include 'module/footer.php';
?>
</body>
</html>