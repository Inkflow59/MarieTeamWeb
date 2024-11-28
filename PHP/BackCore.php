<?php
$db = new mysqli('localhost', 'root', '', 'marieteam');

function getTraversees() {
    global $db;

    $demain = date('Y-m-d', strtotime('+1 day'));

    $sql = "SELECT 
        t.numTra,
        t.date,
        t.heure,
        t.code,
        t.idBat,
        b.nomBat,
        p1.nomPort AS port_depart,
        p2.nomPort AS port_arrivee
    FROM traversee t
    JOIN liaison l ON t.code = l.code
    JOIN port p1 ON l.idPort_Depart = p1.idPort
    JOIN port p2 ON l.idPort_Arrivee = p2.idPort
    JOIN bateau b ON t.idBat = b.idBat
    WHERE t.date >= ?
    ORDER BY t.date ASC, t.heure ASC";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $demain);
    $stmt->execute();
    $result = $stmt->get_result();

    $traverses = [];
    while ($row = $result->fetch_assoc()) {
        $traverse = [
            'numTra' => $row['numTra'],
            'date' => $row['date'],
            'heure' => $row['heure'],
            'code' => $row['code'],
            'idBat' => $row['idBat'],
            'nomBat' => $row['nomBat'],
            'port_depart' => $row['port_depart'],
            'port_arrivee' => $row['port_arrivee']
        ];

        $traverses[] = $traverse;
    }

    return $traverses;
}


function estPlein($traversee) {
    global $db; // Rend la variable $db globale accessible dans la fonction

    // Prépare la requête SQL pour vérifier si le trajet est plein
    $query = "SELECT 
        r.numTra AS numero_trajet,
        b.nomBat AS bateau,
        c.lettre AS categorie,
        SUM(e.quantite) AS quantite_reservee,
        ct.capaciteMax AS capacite_max,
        CASE 
            WHEN SUM(e.quantite) >= ct.capaciteMax THEN 'Plein'
            ELSE 'Disponible'
        END AS etat_trajet
        FROM reservation r
        JOIN enregistrer e ON r.numRes = e.numRes
        JOIN type t ON e.idType = t.idType
        JOIN contenir ct ON t.lettre = ct.lettre
        JOIN bateau b ON ct.idBat = b.idBat
        JOIN categorie c ON t.lettre = c.lettre
        WHERE r.numTra = ?
        GROUP BY r.numTra, b.nomBat, c.lettre, ct.capaciteMax
        ORDER BY r.numTra, c.lettre";

    // Prépare la requête avec le numéro de traversée fourni
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $traversee['numTra']); // On lie le paramètre du numéro de trajet
    $stmt->execute();
    $result = $stmt->get_result();

    // Parcourt les résultats pour vérifier l'état du trajet
    while ($row = $result->fetch_assoc()) {
        if ($row['etat_trajet'] === 'Plein') {
            return true; // Si une catégorie est pleine, on retourne "true"
        }
    }

    return false; // Si aucune catégorie n'est pleine, on retourne "false"
}

function getPlacesDisponiblesParCategorie($traversee) {
    global $db; // Rend la variable $db globale accessible dans la fonction
    
    // Prépare la requête SQL pour calculer les places disponibles par catégorie
    $query = "SELECT 
        c.lettre AS categorie,
        ct.capaciteMax AS capacite_max,
        IFNULL(SUM(e.quantite), 0) AS quantite_reservee,
        (ct.capaciteMax - IFNULL(SUM(e.quantite), 0)) AS places_disponibles
        FROM contenir ct
        JOIN categorie c ON ct.lettre = c.lettre
        JOIN bateau b ON ct.idBat = b.idBat
        LEFT JOIN type t ON c.lettre = t.lettre
        LEFT JOIN enregistrer e ON t.idType = e.idType
        LEFT JOIN reservation r ON e.numRes = r.numRes
        WHERE r.numTra = ?
        GROUP BY c.lettre, ct.capaciteMax
        ORDER BY c.lettre";

    // Prépare la requête avec le numéro de traversée fourni
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $traversee['numTra']); // On lie le paramètre du numéro de trajet
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialise un tableau pour les places disponibles
    $placesDisponibles = [];

    // Parcourt les résultats pour construire le tableau
    while ($row = $result->fetch_assoc()) {
        $placesDisponibles[] = [
            'categorie' => $row['categorie'],
            'capacite_max' => $row['capacite_max'],
            'quantite_reservee' => $row['quantite_reservee'],
            'places_disponibles' => $row['places_disponibles']
        ];
    }

    return $placesDisponibles; // Retourne les données sous forme de tableau
}

function getTraversesBySecteur($idSecteur) {
    global $db; // Rend la variable $db globale accessible dans la fonction

    // Requête SQL pour récupérer les traversées selon l'idSecteur
    $sql = "
        SELECT 
            t.numTra,
            t.date,
            t.heure,
            b.nomBat,
            l.distance,
            p1.nomPort AS port_depart,
            p2.nomPort AS port_arrivee
        FROM 
            traversee t
        INNER JOIN 
            liaison l ON t.code = l.code
        INNER JOIN 
            secteur s ON l.idSecteur = s.idSecteur
        INNER JOIN 
            bateau b ON t.idBat = b.idBat
        INNER JOIN 
            port p1 ON l.idPort_Depart = p1.idPort
        INNER JOIN 
            port p2 ON l.idPort_Arrivee = p2.idPort
        WHERE 
            s.idSecteur = $idSecteur
        ORDER BY 
            t.date, t.heure;
    ";

    // Exécution de la requête
    $result = $db->query($sql);

    // Vérification de l'existence des résultats
    if ($result->num_rows > 0) {
        // Récupération des résultats sous forme de tableau associatif
        $traversees = [];
        while ($row = $result->fetch_assoc()) {
            $traversees[] = $row;
        }
        return $traversees;
    } else {
        return null; // Aucun résultat trouvé
    }
}

function reserverTrajet($numRes, $nomRes, $adresse, $codePostal, $ville, $numTra, $typesQuantites) {
    // Accéder à la variable globale $db pour la connexion
    global $db;

    // Commencer une transaction pour assurer la cohérence des données
    $db->begin_transaction();

    try {
        // Insérer une nouvelle réservation dans la table reservation
        $sql_reservation = "
            INSERT INTO reservation (numRes, nomRes, adresse, codePostal, ville, numTra)
            VALUES (?, ?, ?, ?, ?, ?)
        ";

        $stmt_reservation = $db->prepare($sql_reservation);
        $stmt_reservation->bind_param("issssi", $numRes, $nomRes, $adresse, $codePostal, $ville, $numTra);
        $stmt_reservation->execute();

        // Insérer chaque type de billet et la quantité correspondante dans la table enregistrer
        $sql_enregistrer = "
            INSERT INTO enregistrer (idType, numRes, quantite)
            VALUES (?, ?, ?)
        ";

        $stmt_enregistrer = $db->prepare($sql_enregistrer);

        // Boucle à travers chaque type de billet et sa quantité
        foreach ($typesQuantites as $type => $quantite) {
            // `type` est l'idType du billet et `quantite` est la quantité de ce type
            $stmt_enregistrer->bind_param("iii", $type, $numRes, $quantite);
            $stmt_enregistrer->execute();
        }

        // Valider la transaction
        $db->commit();
        return true; // Réservation réussie

    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $db->rollback();
        return false; // Réservation échouée
    }
}

function consulterReservation($numRes) {
    // Accéder à la variable globale $db pour la connexion
    global $db;

    // Requête pour récupérer toutes les informations liées à la réservation
    $sql = "
        SELECT 
            r.numRes, r.nomRes, r.adresse, r.codePostal, r.ville, r.numTra,
            e.idType, e.quantite, t.libelleType
        FROM reservation r
        LEFT JOIN enregistrer e ON r.numRes = e.numRes
        LEFT JOIN type t ON e.idType = t.idType
        WHERE r.numRes = ?
    ";

    // Préparer la requête
    if ($stmt = $db->prepare($sql)) {
        // Lier les paramètres
        $stmt->bind_param("i", $numRes);
        
        // Exécuter la requête
        $stmt->execute();
        
        // Récupérer les résultats
        $result = $stmt->get_result();
        
        // Vérifier si une réservation existe avec ce numéro
        if ($result->num_rows > 0) {
            // Récupérer les données sous forme de tableau associatif
            $reservation = $result->fetch_assoc();
            
            // Retourner toutes les informations de la réservation
            return $reservation;
        } else {
            // Aucun résultat trouvé pour ce numRes
            return false;
        }
        
    } else {
        // Fermer la requête
        // Vérifier si la préparation de la requête a réussi
        if ($stmt) {
            // Fermer la requête
            $stmt->close();
        }
        // En cas d'erreur lors de la préparation de la requête
        return false;
    }
}

function getHeureArrivee($numTra) {
    // Accéder à la variable globale $db pour la connexion
    global $db;

    // Récupérer les informations de la traversée, y compris l'heure de départ et le temps de liaison
    $sql_traversee = "
        SELECT t.heure, l.tempsLiaison 
        FROM traversée t
        JOIN liaison l ON t.code = l.code
        WHERE t.numTra = ?
    ";

    // Préparer et exécuter la requête
    $stmt = $db->prepare($sql_traversee);
    $stmt->bind_param("i", $numTra); // Binding du numéro de traversée
    $stmt->execute();

    // Récupérer les résultats
    $result = $stmt->get_result();
    
    // Vérifier si une traversée correspondante a été trouvée
    if ($row = $result->fetch_assoc()) {
        // Heure de départ de la traversée
        $heureDepart = $row['heure'];
        
        // Temps de liaison (au format TIME)
        $tempsLiaison = $row['tempsLiaison'];

        // Convertir tempsLiaison en secondes (au format HH:MM:SS)
        list($heures, $minutes, $secondes) = explode(":", $tempsLiaison);
        $tempsLiaisonEnSecondes = ($heures * 3600) + ($minutes * 60) + $secondes;

        // Calculer l'heure d'arrivée en ajoutant le temps de liaison à l'heure de départ
        $heureArrivee = strtotime($heureDepart) + $tempsLiaisonEnSecondes; // Ajouter le temps de liaison en secondes
        $heureArrivee = date('H:i', $heureArrivee); // Formatage de l'heure d'arrivée

        return $heureArrivee;
    }

    // Si aucune traversée n'a été trouvée
    return null;
}

function barreRecherche($nomSecteur) {
    global $db; // Rend la variable $db globale accessible dans la fonction

    // Requête pour récupérer l'idSecteur à partir du nomSecteur
    $sql_idSecteur = "
        SELECT idSecteur 
        FROM secteur 
        WHERE nomSecteur = ?;
    ";

    // Préparer et exécuter la requête pour obtenir l'idSecteur
    $stmt = $db->prepare($sql_idSecteur);
    $stmt->bind_param("s", $nomSecteur); // "s" pour indiquer que c'est une chaîne
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si le secteur existe
    if ($result->num_rows > 0) {
        // Récupérer l'idSecteur
        $row = $result->fetch_assoc();
        $idSecteur = $row['idSecteur'];

        // Requête SQL pour récupérer les traversées selon l'idSecteur
        $sql_traversee = "
            SELECT 
                t.numTra,
                t.date,
                t.heure,
                b.nomBat,
                l.distance,
                p1.nomPort AS port_depart,
                p2.nomPort AS port_arrivee
            FROM 
                traversee t
            INNER JOIN 
                liaison l ON t.code = l.code
            INNER JOIN 
                secteur s ON l.idSecteur = s.idSecteur
            INNER JOIN 
                bateau b ON t.idBat = b.idBat
            INNER JOIN 
                port p1 ON l.idPort_Depart = p1.idPort
            INNER JOIN 
                port p2 ON l.idPort_Arrivee = p2.idPort
            WHERE 
                s.idSecteur = ?
            ORDER BY 
                t.date, t.heure;
        ";

        // Préparer la requête pour récupérer les traversées
        $stmt = $db->prepare($sql_traversee);
        $stmt->bind_param("i", $idSecteur); // "i" pour indiquer que c'est un entier (idSecteur)
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérification de l'existence des résultats
        if ($result->num_rows > 0) {
            // Récupérer les résultats sous forme de tableau associatif
            $traversees = [];
            while ($row = $result->fetch_assoc()) {
                $traversees[] = $row;
            }
            return $traversees;
        } else {
            return null; // Aucun résultat trouvé pour l'idSecteur
        }
    } else {
        // Si le nomSecteur ne correspond à aucun secteur, retourner null
        return null;
    }
}

function getTarifByType($numTra, $idType) {
    global $db; // Rendre la variable $db globale accessible dans la fonction

    // Requête SQL pour récupérer le tarif basé sur le numTra et l'idType
    $sql = "
        SELECT 
            t.tarif
        FROM 
            tarifer t
        INNER JOIN 
            type ty ON t.idType = ty.idType
        INNER JOIN 
            traversee tr ON t.code = tr.code
        WHERE 
            tr.numTra = ? AND t.idType = ?
        LIMIT 1;
    ";

    // Préparer la requête
    $stmt = $db->prepare($sql);

    // Lier les paramètres : numTra (entier) et idType (entier)
    $stmt->bind_param("ii", $numTra, $idType); // "ii" indique que les deux paramètres sont des entiers

    // Exécution de la requête
    $stmt->execute();

    // Récupérer le résultat
    $result = $stmt->get_result();

    // Vérification si un tarif a été trouvé
    if ($result->num_rows > 0) {
        // Récupérer et retourner le tarif
        $row = $result->fetch_assoc();
        return $row['tarif'];
    } else {
        // Aucun tarif trouvé, retourner null ou un message d'erreur
        return null; // Aucun tarif disponible pour cette traversée et ce type
    }
}