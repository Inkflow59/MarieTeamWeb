DROP DATABASE IF EXISTS MarieTeam;
CREATE DATABASE MarieTeam;
USE MarieTeam;

DROP TABLE IF EXISTS Utilisateur ;
CREATE TABLE Utilisateur(idUtilisateur INT AUTO_INCREMENT NOT NULL,
nomUtilisateur VARCHAR(255),
prenomUtilisateur VARCHAR(255),
dateAnnivUti DATE,
PRIMARY KEY (idUtilisateur)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Trajet ;
CREATE TABLE Trajet(idTrajet INT AUTO_INCREMENT NOT NULL,
villeDepart VARCHAR(255),
villeArrivee VARCHAR(255),
heureDepart DATETIME,
heureArrivee DATETIME,
etat VARCHAR(255),
PRIMARY KEY (idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Capitaine ;
CREATE TABLE Capitaine(idCapitaine INT AUTO_INCREMENT NOT NULL,
nomCapitaine VARCHAR(255),
prenomCapitaine VARCHAR(255),
dateAnnivCapi DATE,
PRIMARY KEY (idCapitaine)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Bateau ;
CREATE TABLE Bateau(matricule VARCHAR(255) AUTO_INCREMENT NOT NULL,
modele VARCHAR(255),
marque VARCHAR(255),
capacite VARCHAR(255),
PRIMARY KEY (matricule)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Administrateur ;
CREATE TABLE Administrateur(idAdmin INT AUTO_INCREMENT NOT NULL,
pseudo VARCHAR(255),
idCapitaine INT,
PRIMARY KEY (idAdmin)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Reserver;
CREATE TABLE Reserver(idUtilisateur INT AUTO_INCREMENT NOT NULL,
idTrajet INT NOT NULL,
PRIMARY KEY (idUtilisateur,
 idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Modifier;
CREATE TABLE Modifier(idCapitaine INT AUTO_INCREMENT NOT NULL,
idTrajet INT NOT NULL,
descriptionEtat VARCHAR(255),
PRIMARY KEY (idCapitaine,
 idTrajet)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Detailler;
CREATE TABLE Detailler(idCapitaine INT AUTO_INCREMENT NOT NULL,
matricule VARCHAR(255) NOT NULL,
PRIMARY KEY (idCapitaine,
 matricule)) ENGINE=InnoDB;

DROP TABLE IF EXISTS Avis;
CREATE TABLE Avis(idAvis INT AUTO_INCREMENT NOT NULL,
nomAvis VARCHAR(255),
note FLOAT,
PRIMARY KEY (idAvis)) ENGINE=InnoDB;

ALTER TABLE Administrateur ADD CONSTRAINT FK_Administrateur_idCapitaine FOREIGN KEY (idCapitaine) REFERENCES Capitaine (idCapitaine);
ALTER TABLE Reserver ADD CONSTRAINT FK_Reserver_idUtilisateur FOREIGN KEY (idUtilisateur) REFERENCES Utilisateur (idUtilisateur);
ALTER TABLE Reserver ADD CONSTRAINT FK_Reserver_idTrajet FOREIGN KEY (idTrajet) REFERENCES trajet (idTrajet);
ALTER TABLE Modifier ADD CONSTRAINT FK_Modifier__idCapitaine FOREIGN KEY (idCapitaine) REFERENCES Capitaine (idCapitaine);
ALTER TABLE Modifier ADD CONSTRAINT FK_Modifier__idTrajet FOREIGN KEY (idTrajet) REFERENCES trajet (idTrajet);
ALTER TABLE Detailler ADD CONSTRAINT FK_Detailler_idCapitaine FOREIGN KEY (idCapitaine) REFERENCES Capitaine (idCapitaine);
ALTER TABLE Detailler ADD CONSTRAINT FK_Detailler_matricule FOREIGN KEY (matricule) REFERENCES Bateau (matricule);
