<?php
/**
 * Fichier de fonctions principales pour la gestion des traversées et réservations
 * 
 * Ce fichier contient toutes les fonctions nécessaires pour gérer les traversées,
 * les réservations et les recherches de trajets pour MarieTeam.
 * 
 * @package MarieTeam
 * @subpackage Core
 */

/** @var mysqli $db Connexion à la base de données */
$db = new mysqli('localhost', 'root', '', 'marieteam');

/**
 * Récupère la liste des traversées futures
 * 
 * @param int $limit Nombre maximum de résultats à retourner
 * @param int $offset Position de départ pour la pagination
 * @return array Liste des traversées avec leurs détails
 */
function getTraversees($limit = 25, $offset = 0) {
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
    ORDER BY t.date ASC, t.heure ASC
    LIMIT ? OFFSET ?";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("sii", $demain, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();

    $traverses = [];
    while ($row = $result->fetch_assoc()) {
        $traverses[] = [
            'numTra' => $row['numTra'],
            'date' => $row['date'],
            'heure' => $row['heure'],
            'code' => $row['code'],
            'idBat' => $row['idBat'],
            'nomBat' => $row['nomBat'],
            'port_depart' => $row['port_depart'],
            'port_arrivee' => $row['port_arrivee']
        ];
    }

    return $traverses;
}

/**
 * Compte le nombre total de traversées futures
 * 
 * @return int Nombre total de traversées
 */
function getNombreTotalTraversees() {
    global $db;
    
    $demain = date('Y-m-d', strtotime('+1 day'));
    
    $sql = "SELECT COUNT(*) as total FROM traversee WHERE date >= ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $demain);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Vérifie si une traversée est complète
 * 
 * @param array $traversee Données de la traversée à vérifier
 * @return bool True si la traversée est pleine, False sinon
 */
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

/**
 * Récupère les places disponibles par catégorie pour une traversée
 * 
 * @param array $traversee Données de la traversée
 * @return array Tableau des places disponibles par catégorie
 */
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

/**
 * Récupère les traversées d'un secteur spécifique
 * 
 * @param int $idSecteur Identifiant du secteur
 * @return array|null Liste des traversées du secteur ou null si aucune trouvée
 */
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

/**
 * Effectue une réservation pour une traversée
 * 
 * @param int $numRes Numéro de réservation
 * @param string $nomRes Nom du réservant
 * @param string $adresse Adresse du réservant
 * @param string $codePostal Code postal du réservant
 * @param string $ville Ville du réservant
 * @param int $numTra Numéro de la traversée
 * @param array $typesQuantites Tableau associatif des types et quantités de billets
 * @return bool True si la réservation est réussie, False sinon
 */
function reserverTrajet($numRes, $nomRes, $adresse, $codePostal, $ville, $numTra, $typesQuantites) {
    global $db;

    // Commencer une transaction
    $db->begin_transaction();

    try {
        // 1. Vérifier si la traversée existe et n'est pas déjà passée
        $sql_check_traversee = "SELECT date, heure FROM traversee WHERE numTra = ? AND CONCAT(date, ' ', heure) > NOW()";
        $stmt_check = $db->prepare($sql_check_traversee);
        $stmt_check->bind_param("i", $numTra);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows === 0) {
            throw new Exception("Traversée invalide ou déjà passée");
        }

        // 2. Vérifier les places disponibles pour chaque type
        foreach ($typesQuantites as $idType => $quantite) {
            $sql_check_places = "
                SELECT 
                    ct.capaciteMax - COALESCE(SUM(e.quantite), 0) as places_dispo
                FROM traversee tr
                JOIN liaison l ON tr.code = l.code
                JOIN type t ON t.lettre = (SELECT lettre FROM type WHERE idType = ?)
                JOIN contenir ct ON ct.lettre = t.lettre AND ct.idBat = tr.idBat
                LEFT JOIN reservation r ON r.numTra = tr.numTra
                LEFT JOIN enregistrer e ON e.numRes = r.numRes AND e.idType = t.idType
                WHERE tr.numTra = ?
                GROUP BY ct.capaciteMax";
            
            $stmt_places = $db->prepare($sql_check_places);
            $stmt_places->bind_param("ii", $idType, $numTra);
            $stmt_places->execute();
            $result_places = $stmt_places->get_result();
            $places = $result_places->fetch_assoc();
            
            if (!$places || $places['places_dispo'] < $quantite) {
                throw new Exception("Places insuffisantes pour le type $idType");
            }
        }

        // 3. Insérer la réservation
        $sql_reservation = "
            INSERT INTO reservation (numRes, nomRes, adresse, codePostal, ville, numTra)
            VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt_reservation = $db->prepare($sql_reservation);
        $stmt_reservation->bind_param("issssi", $numRes, $nomRes, $adresse, $codePostal, $ville, $numTra);
        $stmt_reservation->execute();

        // 4. Insérer les détails de la réservation
        $sql_enregistrer = "
            INSERT INTO enregistrer (idType, numRes, quantite)
            VALUES (?, ?, ?)";
        
        $stmt_enregistrer = $db->prepare($sql_enregistrer);
        foreach ($typesQuantites as $idType => $quantite) {
            if ($quantite > 0) {
                $stmt_enregistrer->bind_param("iii", $idType, $numRes, $quantite);
                $stmt_enregistrer->execute();
            }
        }

        // Valider la transaction
        $db->commit();
        return true;

    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $db->rollback();
        error_log("Erreur lors de la réservation : " . $e->getMessage());
        return false;
    }
}

/**
 * Consulte les détails d'une réservation
 * 
 * @param int $numRes Numéro de réservation
 * @return array|false Détails de la réservation ou false si non trouvée
 */
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

/**
 * Calcule l'heure d'arrivée d'une traversée
 * 
 * @param int $numTra Numéro de la traversée
 * @return string|null Heure d'arrivée au format HH:MM ou null si erreur
 */
function getHeureArrivee($numTra) {
    // Accéder à la variable globale $db pour la connexion
    global $db;

    // Récupérer les informations de la traversée, y compris l'heure de départ et le temps de liaison
    $sql_traversee = "
        SELECT t.heure, l.tempsLiaison 
        FROM traversee t
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

/**
 * Recherche des traversées selon des critères spécifiques
 * 
 * @param string $nomSecteur Nom du secteur
 * @param string $date Date de la traversée (format Y-m-d)
 * @param string $villeDepart Ville de départ
 * @param string $villeArrivee Ville d'arrivée
 * @param int $limit Nombre maximum de résultats
 * @param int $offset Position de départ pour la pagination
 * @return array|null Liste des traversées trouvées ou null si aucune
 */
function barreRecherche($nomSecteur, $date, $villeDepart, $villeArrivee, $limit = 25, $offset = 0) {
    global $db;
    
    // Requête pour récupérer l'idSecteur à partir du nomSecteur
    $sql_idSecteur = "
        SELECT idSecteur 
        FROM secteur 
        WHERE nomSecteur = ?;
    ";

    // Préparer et exécuter la requête pour obtenir l'idSecteur
    $stmt = $db->prepare($sql_idSecteur);
    $stmt->bind_param("s", $nomSecteur);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier si le secteur existe
    if ($result->num_rows > 0) {
        // Récupérer l'idSecteur
        $row = $result->fetch_assoc();
        $idSecteur = $row['idSecteur'];

        // Initialiser une variable pour le nombre de tentatives
        $tentatives = 0;
        $maxTentatives = 30; // Limite de tentatives (par exemple, 30 jours)
        
        do {
            // Requête SQL pour récupérer les traversées selon l'idSecteur, la date et les ports
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
                    AND t.date = ? 
                    AND p1.nomPort = ?
                    AND p2.nomPort = ?
                ORDER BY 
                    t.date, t.heure
                LIMIT ? OFFSET ?";

            // Préparer la requête pour récupérer les traversées
            $stmt = $db->prepare($sql_traversee);
            $stmt->bind_param("isssii", $idSecteur, $date, $villeDepart, $villeArrivee, $limit, $offset);
            $stmt->execute();
            $result = $stmt->get_result();

            // Incrémenter la date pour la prochaine tentative
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
            $tentatives++;
        } while ($result->num_rows === 0 && $tentatives < $maxTentatives);

        // Vérification de l'existence des résultats
        if ($result->num_rows > 0) {
            // Récupérer les résultats sous forme de tableau associatif
            $traversees = [];
            while ($row = $result->fetch_assoc()) {
                $traversees[] = $row;
            }
            return $traversees; // Retourner les traversées trouvées
        } else {
            return null; // Aucun résultat trouvé
        }
    } else {
        // Si le nomSecteur ne correspond à aucun secteur, retourner null
        return null;
    }
}

/**
 * Compte le nombre total de résultats d'une recherche
 * 
 * @param string $nomSecteur Nom du secteur
 * @param string $date Date de la traversée
 * @param string $villeDepart Ville de départ
 * @param string $villeArrivee Ville d'arrivée
 * @return int Nombre total de traversées correspondant aux critères
 */
function getNombreTotalRecherche($nomSecteur, $date, $villeDepart, $villeArrivee) {
    global $db;
    
    $sql = "SELECT COUNT(*) as total 
            FROM traversee t
            INNER JOIN liaison l ON t.code = l.code
            INNER JOIN secteur s ON l.idSecteur = s.idSecteur
            INNER JOIN port p1 ON l.idPort_Depart = p1.idPort
            INNER JOIN port p2 ON l.idPort_Arrivee = p2.idPort
            WHERE s.nomSecteur = ? 
            AND t.date = ? 
            AND p1.nomPort = ?
            AND p2.nomPort = ?";
            
    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssss", $nomSecteur, $date, $villeDepart, $villeArrivee);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['total'];
}

/**
 * Récupère le tarif pour un type spécifique sur une traversée
 * 
 * @param int $numTra Numéro de la traversée
 * @param int $idType Identifiant du type de billet
 * @return float|null Tarif trouvé ou null si non trouvé
 */
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

/**
 * Récupère la liste des ports disponibles
 * 
 * @return array|null Liste des noms de ports ou null si aucun port trouvé
 */
function getPorts(){
    global $db;

    $sql = "SELECT nomPort FROM port ORDER BY nomPort ASC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $ports = [];
        while($row = $result->fetch_assoc()) {
            $ports[] = $row['nomPort'];
        }
        return $ports;
    } else {
        return null;
    }
}

/**
 * Récupère les tarifs pour une traversée donnée
 * 
 * @param int $numTra Numéro de la traversée
 * @return array Tableau associatif des tarifs par type de passager/véhicule
 */
function getTarifsByNumTra($numTra) {
    global $db; // Rendre la variable $db globale accessible dans la fonction

    // Requête SQL pour récupérer les tarifs basés sur le numTra
    $sql = "
        SELECT idType, tarif 
        FROM tarifer 
        WHERE code = (SELECT code FROM traversee WHERE numTra = ?)
    ";

    // Préparer la requête
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $numTra); // Lier le paramètre numTra

    // Exécution de la requête
    $stmt->execute();
    $result = $stmt->get_result();

    // Stocker les tarifs dans un tableau
    $tarifs = [];
    while ($row = $result->fetch_assoc()) {
        $tarifs[$row['idType']] = $row['tarif'];
    }

    return $tarifs; // Retourner le tableau des tarifs
}

//Pour stocker le numéro de la traversée lorsque l'utilisateur clique sur le bouton "Suivant"
session_start(); // Assurez-vous que la session est démarrée
if (isset($_POST['numTra'])) {
    $_SESSION['numTra'] = $_POST['numTra']; // Stocke le numéro de traversée en session
}