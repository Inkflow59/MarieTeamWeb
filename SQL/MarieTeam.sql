DROP DATABASE IF EXISTS MarieTeam;
CREATE DATABASE MarieTeam;
USE MarieTeam;

DROP TABLE IF EXISTS Utilisateur ;
CREATE TABLE Utilisateur (idUtilisateur INT AUTO_INCREMENT NOT NULL,
email VARCHAR(255),
password VARCHAR(255),
nomUtilisateur VARCHAR(255),
prenomUtilisateur VARCHAR(255),
dateAnnivUti DATE,
updated_at DATETIME,
created_at DATETIME,
PRIMARY KEY (idUtilisateur)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Trajet ;
CREATE TABLE Trajet (idTrajet INT AUTO_INCREMENT NOT NULL,
villeDepart VARCHAR(255),
villeArrivee VARCHAR(255),
date DATE,
heureDepart TIME,
heureArrivee TIME,
tarifEnfant FLOAT,
tarifAdulte FLOAT,
tarifVoiture FLOAT,
tarifPoidsLourd FLOAT,
etat VARCHAR(255),
updated_at DATETIME,
created_at DATETIME,
PRIMARY KEY (idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Capitaine ;
CREATE TABLE Capitaine (idCapitaine INT AUTO_INCREMENT NOT NULL,
nomCapitaine VARCHAR(255),
prenomCapitaine VARCHAR(255),
dateAnnivCapi DATE,
identifiant VARCHAR(255),
password VARCHAR(255),
updated_at DATETIME,
created_at DATETIME,
PRIMARY KEY (idCapitaine)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Bateau ;
CREATE TABLE Bateau (matricule VARCHAR(255) NOT NULL,
modele VARCHAR(255),
marque VARCHAR(255),
capaciteHumaine INT,
capaciteVehicules INT,
updated_at DATETIME,
created_at DATETIME,
PRIMARY KEY (matricule)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Administrateur ;
CREATE TABLE Administrateur (idAdmin INT AUTO_INCREMENT NOT NULL,
pseudo VARCHAR(255),
emailAdmin VARCHAR(255),
mdp VARCHAR(255),
updated_at DATETIME,
created_at DATETIME,
PRIMARY KEY (idAdmin)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Reserver ;
CREATE TABLE Reserver (idUtilisateur INT AUTO_INCREMENT NOT NULL,
idTrajet INT NOT NULL,
reference TEXT,
nbEnfant INT,
nbAdulte INT,
nbVoiture INT,
nbPoidsLourd INT,
PRIMARY KEY (idUtilisateur,
idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Assigner ;
CREATE TABLE Assigner (idAdmin INT AUTO_INCREMENT NOT NULL,
idCapitaine INT NOT NULL,
idTrajet INT NOT NULL,
PRIMARY KEY (idAdmin,
idCapitaine,
idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Modifier ;
CREATE TABLE Modifier (idCapitaine INT AUTO_INCREMENT NOT NULL,
idTrajet INT NOT NULL,
descriptionEtat VARCHAR(255),
PRIMARY KEY (idCapitaine,
idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Detailler ;
CREATE TABLE Detailler (idCapitaine INT AUTO_INCREMENT NOT NULL,
matricule VARCHAR(255) NOT NULL,
PRIMARY KEY (idCapitaine,
matricule)) ENGINE=InnoDB;

ALTER TABLE Reserver ADD CONSTRAINT FK_Reserver_idUtilisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur);

ALTER TABLE Reserver ADD CONSTRAINT FK_Reserver_idTrajet FOREIGN KEY (idTrajet) REFERENCES Trajet (idTrajet);
ALTER TABLE Assigner ADD CONSTRAINT FK_Assigner_idAdmin FOREIGN KEY (idAdmin) REFERENCES Administrateur (idAdmin);
ALTER TABLE Assigner ADD CONSTRAINT FK_Assigner_idCapitaine FOREIGN KEY (idCapitaine) REFERENCES Capitaine (idCapitaine);
ALTER TABLE Assigner ADD CONSTRAINT FK_Assigner_idTrajet FOREIGN KEY (idTrajet) REFERENCES Trajet (idTrajet);
ALTER TABLE Modifier ADD CONSTRAINT FK_Modifier_idCapitaine FOREIGN KEY (idCapitaine) REFERENCES Capitaine (idCapitaine);
ALTER TABLE Modifier ADD CONSTRAINT FK_Modifier_idTrajet FOREIGN KEY (idTrajet) REFERENCES Trajet (idTrajet);
ALTER TABLE Detailler ADD CONSTRAINT FK_Detailler_idCapitaine FOREIGN KEY (idCapitaine) REFERENCES Capitaine (idCapitaine);
ALTER TABLE Detailler ADD CONSTRAINT FK_Detailler_matricule FOREIGN KEY (matricule) REFERENCES Bateau (matricule);