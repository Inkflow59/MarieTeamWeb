<?php
$db = new mysqli('localhost', 'root', '', 'marieteam');
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

function changementSecteur($codeLiaison, $idNouvSecteur) {
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

function modifierDistance($codeLiaison, $nouvDist) {
    global $db; //Utilisation de la variable globale (connexion à la BDD)

    if($nouvDist < 0) {
        return false; //La distance ne peut pas être négatove (on ne veut pas d'un bateau qui recule)
    }

    $sql = "UPDATE liaison SET distance = $nouvDist WHERE code = $codeLiaison"; //Préparation de la requête
    $result = $db->query($sql); //On exécute la requête

    if($result) {
        return true; //On retourne 'true' si la liaison a bien été mise à jour
    }
    return false; //On retourne 'false" si la liaison n'a pas été modifiée
}