DROP DATABASE IF EXISTS MarieTeam;
CREATE DATABASE MarieTeam;
USE MarieTeam;

DROP TABLE IF EXISTS Utilisateur ;
CREATE TABLE Utilisateur (idUser INT AUTO_INCREMENT NOT NULL,
nom VARCHAR(255),
prenom VARCHAR(255),
email VARCHAR(255),
password VARCHAR(255),
role ENUM("Utilisateur","Capitaine", "Administrateur"),
PRIMARY KEY (idUser)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Liaison ;
CREATE TABLE Liaison (codeLiaison INT AUTO_INCREMENT NOT NULL,
portDepart VARCHAR(255),
portArrivee VARCHAR(255),
distance FLOAT,
temps TIME,
statutLiaison ENUM("A l'heure","En retard","Annulé"),
PRIMARY KEY (codeLiaison)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Bateau ;
CREATE TABLE Bateau (idBateau INT AUTO_INCREMENT NOT NULL,
nom VARCHAR(255),
longueur FLOAT,
largeur FLOAT,
vitesse FLOAT,
type ENUM("Frêt", "Croisière"),
idCap INT,
idEquipement INT,
PRIMARY KEY (idBateau)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Periode ;
CREATE TABLE Periode (idPeriode INT AUTO_INCREMENT NOT NULL,
dateDebut DATE,
dateFin DATE,
PRIMARY KEY (idPeriode)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Equipement ;
CREATE TABLE Equipement (idEquipement INT AUTO_INCREMENT NOT NULL,
nomEquipement VARCHAR(255),
PRIMARY KEY (idEquipement)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Reservation ;
CREATE TABLE Reservation (idReservation INT AUTO_INCREMENT NOT NULL,
dateReservation DATE,
nbPassagers INT,
nbVehicules INT,
nbCamions_Reservation INT,
montant FLOAT,
statutPaiement ENUM("Refusé","Payé", "En attente"),
idUser INT,
idTraversee INT,
PRIMARY KEY (idReservation)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Tarifs ;
CREATE TABLE Tarifs (idTarifs INT AUTO_INCREMENT NOT NULL,
prix FLOAT,
type ENUM("Passager","Voiture","Camion"),
idPeriode INT,
codeLiaison INT,
PRIMARY KEY (idTarifs)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Traversee ;
CREATE TABLE Traversee (idTraversee INT AUTO_INCREMENT NOT NULL,
dateDepart DATE,
heureDepart TIME,
nbPlacesDisponibles INT,
idBateau INT,
codeLiaison INT,
idCapacite INT,
PRIMARY KEY (idTraversee)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Capitaine ;
CREATE TABLE Capitaine (idCap INT AUTO_INCREMENT NOT NULL,
nomCap VARCHAR(255),
prenomCap VARCHAR(255),
idUser INT,
idBateau INT,
PRIMARY KEY (idCap)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Capacite ;
CREATE TABLE Capacite (idCapacite INT AUTO_INCREMENT NOT NULL,
capaciteHumaine INT,
capaciteVoitures INT,
capaciteCamions INT,
idTraversee INT,
PRIMARY KEY (idCapacite)) ENGINE=InnoDB;

ALTER TABLE Bateau ADD CONSTRAINT FK_Bateau_idCap FOREIGN KEY (idCap) REFERENCES Capitaine (idCap);
ALTER TABLE Bateau ADD CONSTRAINT FK_Bateau_idEquipement FOREIGN KEY (idEquipement) REFERENCES Equipement (idEquipement);
ALTER TABLE Reservation ADD CONSTRAINT FK_Reservation_idUser FOREIGN KEY (idUser) REFERENCES Utilisateur (idUser);
ALTER TABLE Reservation ADD CONSTRAINT FK_Reservation_idTraversee FOREIGN KEY (idTraversee) REFERENCES Traversee (idTraversee);
ALTER TABLE Tarifs ADD CONSTRAINT FK_Tarifs_idPeriode FOREIGN KEY (idPeriode) REFERENCES Periode (idPeriode);
ALTER TABLE Tarifs ADD CONSTRAINT FK_Tarifs_codeLiaison FOREIGN KEY (codeLiaison) REFERENCES Liaison (codeLiaison);
ALTER TABLE Traversee ADD CONSTRAINT FK_Traversee_idBateau FOREIGN KEY (idBateau) REFERENCES Bateau (idBateau);
ALTER TABLE Traversee ADD CONSTRAINT FK_Traversee_codeLiaison FOREIGN KEY (codeLiaison) REFERENCES Liaison (codeLiaison);
ALTER TABLE Traversee ADD CONSTRAINT FK_Traversee_idCapacite FOREIGN KEY (idCapacite) REFERENCES Capacite (idCapacite);
ALTER TABLE Capitaine ADD CONSTRAINT FK_Capitaine_idUser FOREIGN KEY (idUser) REFERENCES Utilisateur (idUser);
ALTER TABLE Capitaine ADD CONSTRAINT FK_Capitaine_idBateau FOREIGN KEY (idBateau) REFERENCES Bateau (idBateau);
ALTER TABLE Capacite ADD CONSTRAINT FK_Capacite_idTraversee FOREIGN KEY (idTraversee) REFERENCES Traversee (idTraversee);