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
 * @param int $traversee Numéro de la traversée
 * @return array Tableau des places disponibles par catégorie
 */
function getPlacesDisponiblesParCategorie($traversee) {
    global $db;
    
    // Requête SQL modifiée pour être plus robuste
    $query = "SELECT 
        c.lettre,
        c.libelleCat,
        COALESCE(SUM(ct.capaciteMax), 0) as capaciteMax,
        COALESCE(SUM(e.quantite), 0) as places_occupees,
        COALESCE(SUM(ct.capaciteMax) - COALESCE(SUM(e.quantite), 0), 0) as places_disponibles
    FROM traversee t
    CROSS JOIN categorie c 
    LEFT JOIN bateau b ON t.idBat = b.idBat
    LEFT JOIN contenir ct ON b.idBat = ct.idBat AND c.lettre = ct.lettre
    LEFT JOIN type ty ON c.lettre = ty.lettre
    LEFT JOIN enregistrer e ON ty.idType = e.idType
    LEFT JOIN reservation r ON e.numRes = r.numRes AND r.numTra = t.numTra
    WHERE t.numTra = ? AND c.lettre IN ('P', 'V')
    GROUP BY c.lettre, c.libelleCat
    ORDER BY c.lettre";

    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $traversee);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialisation des valeurs par défaut
    $placesDisponibles = [
        'passagers' => 0,
        'vehicules' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        if ($row['lettre'] === 'P') {
            $placesDisponibles['passagers'] = max(0, (int)$row['places_disponibles']);
        } else if ($row['lettre'] === 'V') {
            $placesDisponibles['vehicules'] = max(0, (int)$row['places_disponibles']);
        }
    }
    
    return $placesDisponibles;
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
 * Réserve une traversée pour un client
 * 
 * @param string $numRes Numéro de réservation unique
 * @param string $nom Nom du client
 * @param string $adresse Adresse du client
 * @param string $codePostal Code postal du client
 * @param string $ville Ville du client
 * @param int $numTra Numéro de la traversée
 * @param array $quantites Tableau associatif des quantités par type [idType => quantite]
 * @return bool True si la réservation est réussie, False sinon
 */
function reserverTrajet($numRes, $nom, $adresse, $codePostal, $ville, $numTra, $quantites) {
    global $db;

    try {
        // Vérification des données d'entrée
        if (!is_array($quantites)) {
            throw new Exception("Le format des quantités est invalide");
        }

        // Vérification qu'au moins une quantité est supérieure à 0
        $hasQuantity = false;
        foreach ($quantites as $quantite) {
            if ($quantite > 0) {
                $hasQuantity = true;
                break;
            }
        }
        if (!$hasQuantity) {
            throw new Exception("Aucune quantité sélectionnée");
        }

        // Début de la transaction
        $db->begin_transaction();

        try {
            // Insertion de la réservation
            $stmt = $db->prepare("INSERT INTO reservation (numRes, nomRes, adresse, codePostal, ville, numTra) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$stmt) {
                throw new Exception("Erreur de préparation de la requête");
            }

            $stmt->bind_param("sssssi", $numRes, $nom, $adresse, $codePostal, $ville, $numTra);
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de l'insertion de la réservation");
            }

            // Insertion des types et quantités
            $stmtTypes = $db->prepare("INSERT INTO enregistrer (numRes, idType, quantite) VALUES (?, ?, ?)");
            if (!$stmtTypes) {
                throw new Exception("Erreur de préparation de la requête des types");
            }

            // Parcourir le tableau des quantités et insérer uniquement celles > 0
            foreach ($quantites as $idType => $quantite) {
                if ($quantite > 0) {
                    $stmtTypes->bind_param("sii", $numRes, $idType, $quantite);
                    if (!$stmtTypes->execute()) {
                        throw new Exception("Erreur lors de l'insertion du type $idType");
                    }
                }
            }

            // Validation de la transaction
            $db->commit();
            return true;

        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    } catch (Exception $e) {
        throw new Exception("Erreur lors de la réservation: " . $e->getMessage());
    }
}

/**
 * Consulte les détails d'une réservation
 * 
 * @param int $numRes Numéro de réservation
 * @return array|false Détails de la réservation ou false si non trouvée
 */
function consulterReservation($numRes) {
    global $db;

    // D'abord, récupérer les informations de base de la réservation
    $sql = "
        SELECT 
            numRes, nomRes, adresse, codePostal, ville, numTra
        FROM reservation 
        WHERE numRes = ?
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $numRes);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return false;
    }

    $reservation = $result->fetch_assoc();

    // Ensuite, récupérer les types et quantités associés
    $sql_types = "
        SELECT 
            e.idType, 
            e.quantite, 
            t.libelleType
        FROM enregistrer e
        LEFT JOIN type t ON e.idType = t.idType
        WHERE e.numRes = ?
    ";

    $stmt = $db->prepare($sql_types);
    $stmt->bind_param("s", $numRes);
    $stmt->execute();
    $result_types = $stmt->get_result();

    // Ajouter les types et quantités à la réservation
    $reservation['types'] = [];
    while ($row = $result_types->fetch_assoc()) {
        $reservation['types'][] = [
            'idType' => $row['idType'],
            'quantite' => $row['quantite'],
            'libelleType' => $row['libelleType']
        ];
    }

    return $reservation;
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

function getTempsTotalTraversee($numTra){
    global $db;
    
    // Récupérer l'heure de départ et le temps de liaison
    $sql = "SELECT t.heure as heure_depart, l.tempsLiaison 
            FROM traversee t
            JOIN liaison l ON t.code = l.code
            WHERE t.numTra = ?";
            
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $numTra);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $heureDepart = strtotime($row['heure_depart']);
        $tempsLiaison = $row['tempsLiaison'];
        
        // Convertir le temps de liaison en secondes
        list($heures, $minutes, $secondes) = explode(':', $tempsLiaison);
        $tempsLiaisonSecondes = ($heures * 3600) + ($minutes * 60) + $secondes;
        
        // Calculer l'heure d'arrivée
        $heureArrivee = $heureDepart + $tempsLiaisonSecondes;
        
        // Formater le temps total au format HH:MM
        $tempsTotalSecondes = $tempsLiaisonSecondes;
        $heures = floor($tempsTotalSecondes / 3600);
        $minutes = floor(($tempsTotalSecondes % 3600) / 60);
        
        return sprintf("%02dh%02d", $heures, $minutes);
    }
    
    return null;
}

//Pour stocker le numéro de la traversée lorsque l'utilisateur clique sur le bouton "Suivant"
session_start(); // Assurez-vous que la session est démarrée
if (isset($_POST['numTra'])) {
    $_SESSION['numTra'] = $_POST['numTra']; // Stocke le numéro de traversée en session
}

$places = getPlacesDisponiblesParCategorie(5);
$places["passagers"];