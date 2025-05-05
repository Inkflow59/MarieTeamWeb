# MarieTeam - Système de réservation de traversées maritimes

![MarieTeam](img/index/article2.jpg)

## 📋 Description du projet

MarieTeam est une application web de gestion et de réservation de traversées maritimes. Ce système permet aux clients de rechercher, consulter et réserver des billets pour des traversées en bateau, et aux administrateurs de gérer l'ensemble des opérations liées aux traversées, bateaux, et réservations.

## 🛠️ Fonctionnalités principales

### 🧑‍💻 Interface Client
- **Recherche de traversées** : Filtrage par secteur, port de départ/arrivée et date
- **Réservation de billets** : Processus simplifié en plusieurs étapes
- **Sélection de passagers** : Différents types (adulte, enfant, etc.) avec tarifs adaptés
- **Consultation de billets** : Recherche par numéro de réservation
- **Génération de billets PDF** : Tickets au format PDF téléchargeables

### 👨‍💼 Interface Administrateur
- **Tableau de bord** : Vue d'ensemble des statistiques (chiffre d'affaires, nombre de passagers)
- **Gestion des liaisons** : Ajout/modification/suppression de liaisons maritimes
- **Gestion des traversées** : Planification et modification des traversées
- **Gestion des bateaux** : Ajout et modification des informations sur les bateaux
- **Visualisation des réservations** : Consultation et filtrage des réservations clients
- **Gestion des utilisateurs administrateurs** : Création de comptes administrateurs

## 🚀 Installation

### Prérequis
- PHP 7.4+ 
- MySQL/MariaDB
- Serveur web (Apache, Nginx)
- Composer

### Étapes d'installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-username/MarieTeamWeb.git
   cd MarieTeamWeb
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configuration de la base de données**
   - Créer une base de données nommée `marieteam`
   - Importer les fichiers SQL dans l'ordre suivant:
     ```bash
     mysql -u root -p marieteam < sql/structureBDD.sql
     mysql -u root -p marieteam < sql/dumpBDD.sql
     mysql -u root -p marieteam < sql/tableTarifer.sql
     mysql -u root -p marieteam < sql/habilitations.sql
     ```

4. **Créer le premier administrateur**
   - Accéder à `http://votre-serveur/php/create_first_admin.php`
   - Identifiants par défaut : 
     - Utilisateur : `admin`
     - Mot de passe : `admin123`
   - **Important** : Changer le mot de passe après la première connexion et supprimer/sécuriser le fichier `create_first_admin.php`

5. **Configuration de l'application**
   - Vérifier/ajuster les paramètres de connexion à la base de données dans `php/BackCore.php`

## 🔧 Structure du projet

```
MarieTeamWeb/
├── admin/                  # Interface d'administration
├── css/                    # Fichiers de style
├── img/                    # Images et ressources graphiques
├── js/                     # Scripts JavaScript
├── module/                 # Composants modulaires (header, footer)
├── php/                    # Logique métier et fonctions PHP
├── sql/                    # Scripts de base de données
└── vendor/                 # Dépendances (généré par Composer)
```

## 🔐 Sécurité et utilisateurs

Le système dispose de plusieurs niveaux d'accès:
- **admin_marieteam** : Administrateur avec tous les droits
- **agent_reservation** : Gestion des réservations
- **gestionnaire_traversee** : Gestion des traversées
- **lecture_seule** : Accès en lecture seule pour rapports
- **api_user** : Intégrations externes

## 🌐 Technologies utilisées

- **Backend** : PHP
- **Base de données** : MySQL/MariaDB
- **Frontend** : HTML, CSS, JavaScript
- **Génération de PDF** : TCPDF
- **Styles** : CSS personnalisé, Tailwind CSS, Flowbite

## 📱 Compatibilité

L'application est conçue pour être responsive et compatible avec:
- Ordinateurs de bureau
- Tablettes
- Smartphones

## 📄 Licence

Ce projet est sous licence propriétaire. Tous droits réservés.

## 👥 Contributeurs

- **Tom CUCHEROSSET** : Développeur principal de l'API et des fonctionnalités backend
- **Esteban FAUCQUENOY** : Développeur assistant de l'API et designer principal
- **Sana EL HADDOUCHI** : Développeuse assistante de L'API et designeuse assistante

## 📞 Contact

Pour toute question ou suggestion, veuillez contacter l'équipe de développement MarieTeam.

---

© 2025 MarieTeam - Système de réservation de traversées maritimes