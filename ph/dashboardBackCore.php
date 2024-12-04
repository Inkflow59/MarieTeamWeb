<?php
$db = new mysqli('localhost', 'root', '', 'marieteam');

function connexionAdmin($email, $password) {
    global $db; // Utilisation de la variable globale (connexion à la BDD)

    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Préparation de la requête pour récupérer l'utilisateur par email
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérification si l'utilisateur existe
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérification du mot de passe
        if (password_verify($password, $user['password'])) {
            $_SESSION['idAdmin'] = $user['idAdmin']; // Stocker l'idAdmin dans la session
            return true; // Connexion réussie
        }
    }
    return false; // Échec de la connexion
}

function ajoutLiaison($distance, $idSecteur, $idDepart, $idArrivee, $tempsLiaison) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    $sql = "INSERT INTO liaison (`distance`, `idSecteur`, `idDepart`, `idArrivee`, `temps`) VALUES ('$distance', '$idSecteur', '$idDepart', '$idArrivee', '$tempsLiaison')"; //Préparation de la requête
    $result = $db->query($sql); //Exécution de la requête
    if($result) {
        return true; //On retourne 'true' si la liason a été créée
    }
    return false; //On retourne 'false' si la liaison n'a pas été ajoutée
}

function modifTempsLiaison($codeLiaison, $nouvTemps) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    $sql = "UPDATE liaison SET tempsLiaison = '$nouvTemps' WHERE code = '$codeLiaison'"; //Préparation de la requête
    $result = $db->query($sql); //Exécution de la requête

    if($result) {
        return true; //On retourne 'true' si la liaison a bien été mise à jour
    }
    return false; //On retourne 'false" si la liaison n'a pas été modifiée
}

function modifDepartLiaison($codeLiaison, $idPort) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    if($idPort > 40) {
        return false; //Erreur : il n'existe que 40 ports disponibles
    }

    $checkArr = "SELECT idPort_Arrivee FROM liaison WHERE code = '$codeLiaison'"; //On regarde l'id du port d'arrivée
    $checkResArr = $db->query($checkArr); //On effectue la vérification
    $tabArr = $checkResArr->fetch_assoc(); //On récupère le tableau des réponses

    if($tabArr[0] == $idPort) {
        return false; //Erreur : on ne peut pas partir du port de destination
    }

    $check = "SELECT idPort_Depart FROM liaison WHERE code = '$codeLiaison'"; //On regarde l'id du port de départ déjà inscrit
    $checkRes = $db->query($check); //On effectue la vérification
    $tab = $checkRes->fetch_assoc(); //On récupère le tableau des réponses

    if($tab[0] == $idPort) {
        return false; //Erreur : l'id du port renseigné est le même que celui déjà mis dans la BDD
    }

    $sql = "UPDATE liaison SET idPort_Depart = '$idPort' WHERE code = '$codeLiaison'"; //Préparation de la requête
    $result = $db->query($sql); //Exécution de la requête

    if($result) {
        return true; //On retourne 'true' si la liaison a bien été mise à jour
    }
    return false; //On retourne 'false" si la liaison n'a pas été modifiée
}

function modifArriveeLiaison($codeLiaison, $idPort) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    if($idPort > 40) {
        return false; //Erreur : il n'existe que 40 ports disponibles
    }

    $check = "SELECT idPort_Arrivee FROM liaison WHERE code = '$codeLiaison'"; //On regarde l'id du port d'arrivée déjà inscrit
    $checkRes = $db->query($check); //On effectue la vérification
    $tab = $checkRes->fetch_assoc(); //On récupère le tableau des réponses

    if($tab[0] == $idPort) {
        return false; //Erreur : l'id du port renseigné est le même que celui déjà mis dans la BDD
    }

    $checkDep = "SELECT idPort_Depart FROM liaison WHERE code = '$codeLiaison'"; //On regarde l'id du port de départ
    $checkResDep = $db->query($checkDep); //On effectue la vérification
    $tabDep = $checkResDep->fetch_assoc(); //On récupère le tableau des réponses

    if($tabDep[0] == $idPort) {
        return false; //Erreur : on ne peut pas avoir comme destination le port de départ
    }

    $sql = "UPDATE liaison SET idPort_Arrivee = '$idPort' WHERE code = '$codeLiaison'"; //Préparation de la requête
    $result = $db->query($sql); //Exécution de la requête

    if($result) {
        return true; //On retourne 'true' si la liaison a bien été mise à jour
    }
    return false; //On retourne 'false" si la liaison n'a pas été modifiée
}

function modifSecteur($codeLiaison, $idNouvSecteur) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    if($idNouvSecteur > 4) {
        return false; //Erreur : il n'y a que 4 secteurs disponibles
    }

    $check = "SELECT idSecteur FROM liaison WHERE code = '$codeLiaison'"; //On cherche le secteur de la liason renseignée
    $checkRes = $db->query($check)->fetch_assoc(); //On récupère l'id du secteir

    if($checkRes[0] == $idNouvSecteur) {
        return false; //Erreur : l'id du secteur renseigné est déjà appliqué à la liaison
    }

    $sql = "UPDATE liaison SET idSecteur = '$idNouvSecteur' WHERE code = '$codeLiaison'"; //Préparation de la requête
    $result = $db->query($sql); //Exécution de la requête

    if($result) {
        return true; //On retourne 'true' si la liaison a bien été mise à jour
    }
    return false; //On retourne 'false" si la liaison n'a pas été modifiée
}

function modifDistance($codeLiaison, $nouvDist) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    if($nouvDist < 0) {
        return false; //La distance ne peut pas être négative (on ne veut pas d'un bateau qui recule)
    }

    $sql = "UPDATE liaison SET distance = $nouvDist WHERE code = $codeLiaison"; //Préparation de la requête
    $result = $db->query($sql); //On exécute la requête

    if($result) {
        return true; //On retourne 'true' si la liaison a bien été mise à jour
    }
    return false; //On retourne 'false" si la liaison n'a pas été modifiée
}

function beneficesGeneres() {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    //Préparation de la requête pour calculer le montant total des réservations
    $sql = "
        SELECT SUM(tarif * e.quantite) AS totalBenefices,
            AVG(tarif * e.quantite) AS moyenneBenefices
        FROM reservation r
        JOIN enregistrer e ON r.numRes = e.numRes
        JOIN tarifer t ON e.idType = t.idType
    ";

    $result = $db->query($sql); //Exécution de la requête

    if ($result) {
        $row = $result->fetch_assoc(); //Récupération du résultat
        return [
            'totalBenefices'=> $row['totalBenefices'] ? $row['totalBenefices'] : 0,
            'moyenneBenefices'=> $row['moyenneBenefices'] ? $row['moyenneBenefices'] : 0
        ]; //Retourne le total ou 0 si aucun bénéfice
    }

    return 0; //Retourne 0 en cas d'erreur
}

function getAllReservations() {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    //Préparation de la requête pour récupérer le nombre total de réservations et le nombre de places par type
    $sql = "
        SELECT 
            COUNT(r.numRes) AS nbReservationsTotal,
            SUM(CASE WHEN t.libelleType = 'Adulte' THEN e.quantite ELSE 0 END) AS nbPlacesAdulte,
            SUM(CASE WHEN t.libelleType = 'Enfant' THEN e.quantite ELSE 0 END) AS nbPlacesEnfant,
            SUM(CASE WHEN t.libelleType = 'Senior' THEN e.quantite ELSE 0 END) AS nbPlacesSenior,
            SUM(CASE WHEN t.libelleType = 'Voiture' THEN e.quantite ELSE 0 END) AS nbPlacesVoiture,
            SUM(CASE WHEN t.libelleType = 'Moto' THEN e.quantite ELSE 0 END) AS nbPlacesMoto,
            SUM(CASE WHEN t.libelleType = 'Camion' THEN e.quantite ELSE 0 END) AS nbPlacesCamion,
            SUM(CASE WHEN t.libelleType = 'Camping-car' THEN e.quantite ELSE 0 END) AS nbPlacesCampingCar
        FROM reservation r
        LEFT JOIN enregistrer e ON r.numRes = e.numRes
        LEFT JOIN type t ON e.idType = t.idType
    ";

    $result = $db->query($sql); //Exécution de la requête

    if ($result) {
        $row = $result->fetch_assoc(); //Récupération du résultat
        return [
            'nbReservationsTotal' => $row['nbReservationsTotal'] ? $row['nbReservationsTotal'] : 0,
            'nbPlacesAdulte' => $row['nbPlacesAdulte'] ? $row['nbPlacesAdulte'] : 0,
            'nbPlacesEnfant' => $row['nbPlacesEnfant'] ? $row['nbPlacesEnfant'] : 0,
            'nbPlacesSenior' => $row['nbPlacesSenior'] ? $row['nbPlacesSenior'] : 0,
            'nbPlacesVoiture' => $row['nbPlacesVoiture'] ? $row['nbPlacesVoiture'] : 0,
            'nbPlacesMoto' => $row['nbPlacesMoto'] ? $row['nbPlacesMoto'] : 0,
            'nbPlacesCamion' => $row['nbPlacesCamion'] ? $row['nbPlacesCamion'] : 0,
            'nbPlacesCampingCar' => $row['nbPlacesCampingCar'] ? $row['nbPlacesCampingCar'] : 0,
        ]; //Retourne le tableau avec les données
    }

    return []; //Retourne un tableau vide en cas d'erreur
}

function getAllTraversees() {
    global $db; //Variable de connexion à la base de données

    $sql = "SELECT * FROM `traversees`"; //Requête SQL
    $result = $db->query($sql); //Exécution de la requête SQL

    if ($result) { //Vérification de la validité de la requête SQL
        $rows = []; //Initialisation du tableau pour stocker les données
        while ($row = $result->fetch_assoc()) { //Récupération des données ligne par ligne
            $rows[] = $row; //Ajout de chaque ligne au tableau
        }
        return $rows; //Retourne le tableau des données
    }
    return []; //Retourne un tableau vide en cas d'erreur
}