-- Script de création des habilitations pour MarieTeam
-- Date de création: 30 avril 2025

-- Suppression des utilisateurs s'ils existent déjà
DROP USER IF EXISTS 'admin_marieteam'@'localhost';
DROP USER IF EXISTS 'agent_reservation'@'localhost';
DROP USER IF EXISTS 'gestionnaire_traversee'@'localhost';
DROP USER IF EXISTS 'lecture_seule'@'localhost';
DROP USER IF EXISTS 'api_user'@'localhost';

-- Création des utilisateurs
-- 1. Administrateur avec tous les droits
CREATE USER 'admin_marieteam'@'localhost' IDENTIFIED BY 'AdminM@rieT3@m2025';
GRANT ALL PRIVILEGES ON marieteam.* TO 'admin_marieteam'@'localhost';

-- 2. Agent de réservation: peut gérer les réservations mais pas modifier les traversées
CREATE USER 'agent_reservation'@'localhost' IDENTIFIED BY 'Agent@R3serv2025';
GRANT SELECT ON marieteam.* TO 'agent_reservation'@'localhost';
GRANT INSERT, UPDATE, DELETE ON marieteam.reservation TO 'agent_reservation'@'localhost';
GRANT INSERT, UPDATE, DELETE ON marieteam.enregistrer TO 'agent_reservation'@'localhost';
GRANT SELECT, EXECUTE ON marieteam.* TO 'agent_reservation'@'localhost';

-- 3. Gestionnaire de traversées: peut gérer les traversées mais pas les réservations
CREATE USER 'gestionnaire_traversee'@'localhost' IDENTIFIED BY 'G3st!0nTr@v3rsee';
GRANT SELECT ON marieteam.* TO 'gestionnaire_traversee'@'localhost';
GRANT INSERT, UPDATE, DELETE ON marieteam.traversee TO 'gestionnaire_traversee'@'localhost';
GRANT INSERT, UPDATE, DELETE ON marieteam.liaison TO 'gestionnaire_traversee'@'localhost';
GRANT INSERT, UPDATE, DELETE ON marieteam.bateau TO 'gestionnaire_traversee'@'localhost';
GRANT SELECT, EXECUTE ON marieteam.* TO 'gestionnaire_traversee'@'localhost';

-- 4. Utilisateur en lecture seule: pour les rapports et statistiques
CREATE USER 'lecture_seule'@'localhost' IDENTIFIED BY 'L3cture$3ule';
GRANT SELECT ON marieteam.* TO 'lecture_seule'@'localhost';

-- 5. Utilisateur API: pour les intégrations externes (applications mobiles, site web)
CREATE USER 'api_user'@'localhost' IDENTIFIED BY 'Api@MarieTeam2025';
GRANT SELECT ON marieteam.* TO 'api_user'@'localhost';
GRANT INSERT, UPDATE ON marieteam.reservation TO 'api_user'@'localhost';
GRANT INSERT ON marieteam.enregistrer TO 'api_user'@'localhost';

-- Création de vues sécurisées

-- Vue des traversées disponibles (sans informations sensibles)
CREATE OR REPLACE VIEW marieteam.v_traversees_disponibles AS
SELECT 
    t.numTra,
    t.date,
    t.heure,
    b.nomBat,
    p1.nomPort AS port_depart,
    p2.nomPort AS port_arrivee,
    s.nomSecteur,
    l.distance,
    l.tempsLiaison
FROM traversee t
JOIN liaison l ON t.code = l.code
JOIN port p1 ON l.idPort_Depart = p1.idPort
JOIN port p2 ON l.idPort_Arrivee = p2.idPort
JOIN bateau b ON t.idBat = b.idBat
JOIN secteur s ON l.idSecteur = s.idSecteur
WHERE t.date >= CURRENT_DATE;

-- Vue des réservations anonymisées pour statistiques
CREATE OR REPLACE VIEW marieteam.v_reservations_stats AS
SELECT 
    YEAR(t.date) AS annee,
    MONTH(t.date) AS mois,
    p1.nomPort AS port_depart,
    p2.nomPort AS port_arrivee,
    s.nomSecteur,
    COUNT(r.numRes) AS nombre_reservations,
    SUM(e.quantite) AS nombre_passagers
FROM reservation r
JOIN traversee t ON r.numTra = t.numTra
JOIN liaison l ON t.code = l.code
JOIN port p1 ON l.idPort_Depart = p1.idPort
JOIN port p2 ON l.idPort_Arrivee = p2.idPort
JOIN secteur s ON l.idSecteur = s.idSecteur
JOIN enregistrer e ON r.numRes = e.numRes
GROUP BY YEAR(t.date), MONTH(t.date), p1.nomPort, p2.nomPort, s.nomSecteur;

-- Procédure stockée pour vérifier la disponibilité
DELIMITER $$
CREATE PROCEDURE marieteam.check_disponibilite(IN p_numTra INT, OUT places_disponibles INT)
BEGIN
    DECLARE capacite_totale INT;
    DECLARE places_reservees INT;
    
    -- Récupérer la capacité totale du bateau
    SELECT SUM(c.capaciteMax)
    INTO capacite_totale
    FROM traversee t
    JOIN bateau b ON t.idBat = b.idBat
    JOIN contenir c ON b.idBat = c.idBat
    WHERE t.numTra = p_numTra;
    
    -- Récupérer les places réservées
    SELECT COALESCE(SUM(e.quantite), 0)
    INTO places_reservees
    FROM reservation r
    JOIN enregistrer e ON r.numRes = e.numRes
    WHERE r.numTra = p_numTra;
    
    -- Calculer les places disponibles
    SET places_disponibles = capacite_totale - places_reservees;
END$$
DELIMITER ;

-- Application des privilèges
FLUSH PRIVILEGES;

-- Message de confirmation
SELECT 'Habilitations créées avec succès' AS message;
