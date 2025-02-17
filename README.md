# Documentation du fichier CORE du back

Ce fichier contient plusieurs fonctions utilisées pour interagir avec la base de données d'une application de gestion de traversées maritimes. Les fonctions incluent des opérations de récupération d'informations sur les traversées, la gestion des réservations, et la vérification des places disponibles sur un bateau.

## Connexion à la Base de Données

La connexion à la base de données est établie via `mysqli` à l'aide de la fonction `new mysqli()`.

```php
$db = new mysqli('localhost', 'root', '', 'marieteam');
```

## Fonction `getTraverses`

Récupère toutes les traversées dont la date est supérieure ou égale à la date du jour suivant.

### Détails

- **Requête** : Sélectionne toutes les traversées avec une date supérieure ou égale à la date de demain.
- **Paramètres** : Aucun
- **Retour** : Un tableau associatif des traversées futures.

```php
function getTraverses() { ... }
```

## Fonction `estPlein`

Vérifie si une traversée donnée est pleine, en fonction des réservations effectuées.

### Détails

- **Requête** : Sélectionne les réservations et compare la quantité réservée avec la capacité maximale pour chaque catégorie de place.
- **Paramètres** :
    - `$traversee` : Un tableau contenant les informations de la traversée, avec le champ `numTra`.
- **Retour** : `true` si la traversée est pleine, sinon `false`.

```php
function estPlein($traversee) { ... }
```

## Fonction `getPlacesDisponiblesParCategorie`

Récupère le nombre de places disponibles par catégorie pour une traversée donnée.

### Détails

- **Requête** : Sélectionne la capacité maximale et la quantité réservée par catégorie, puis calcule les places disponibles.
- **Paramètres** :
    - `$traversee` : Un tableau contenant les informations de la traversée.
- **Retour** : Un tableau des places disponibles par catégorie.

```php
function getPlacesDisponiblesParCategorie($traversee) { ... }
```

## Fonction `getTraversesBySecteur`

Récupère les traversées pour un secteur donné.

### Détails

- **Requête** : Sélectionne toutes les traversées d'un secteur spécifique, triées par date et heure.
- **Paramètres** :
    - `$idSecteur` : L'identifiant du secteur.
- **Retour** : Un tableau des traversées pour ce secteur.

```php
function getTraversesBySecteur($idSecteur) { ... }
```

## Fonction `reserverTrajet`

Permet de faire une réservation pour un trajet, en insérant une entrée dans les tables `reservation` et `enregistrer`.

### Détails

- **Transaction** : La fonction utilise une transaction pour garantir l'intégrité des données.
- **Paramètres** :
    - `$numRes` : Le numéro de la réservation.
    - `$nomRes`, `$adresse`, `$codePostal`, `$ville` : Les informations du client.
    - `$numTra` : Le numéro de la traversée.
    - `$typesQuantites` : Un tableau des types de billets et de leurs quantités respectives.
- **Retour** : `true` si la réservation est réussie, sinon `false`.

```php
function reserverTrajet($numRes, $nomRes, $adresse, $codePostal, $ville, $numTra, $typesQuantites) { ... }
```

## Fonction `consulterReservation`

Permet de consulter les détails d'une réservation à partir de son numéro.

### Détails

- **Requête** : Récupère toutes les informations liées à une réservation.
- **Paramètres** :
    - `$numRes` : Le numéro de la réservation.
- **Retour** : Un tableau contenant les détails de la réservation ou `false` si non trouvé.

```php
function consulterReservation($numRes) { ... }
```

## Fonction `getHeureArrivee`

Calcule l'heure d'arrivée d'une traversée, en fonction de l'heure de départ et du temps de liaison.

### Détails

- **Requête** : Sélectionne l'heure de départ et le temps de liaison de la traversée.
- **Paramètres** :
    - `$numTra` : Le numéro de la traversée.
- **Retour** : L'heure d'arrivée calculée au format `HH:mm`.

```php
function getHeureArrivee($numTra) { ... }
```

## Fonction `barreRecherche`

Récupère les traversées d'un secteur donné à partir de son nom.

### Détails

- **Requête** : Récupère l'ID du secteur à partir de son nom, puis sélectionne les traversées associées.
- **Paramètres** :
    - `$nomSecteur` : Le nom du secteur.
- **Retour** : Un tableau des traversées du secteur, ou `null` si aucun secteur n'est trouvé.

```php
function barreRecherche($nomSecteur) { ... }
```

## Fonction `getTarifByType`

Récupère le tarif d'une traversée pour un type de billet donné.

### Détails

- **Requête** : Sélectionne le tarif associé à un type de billet pour une traversée donnée.
- **Paramètres** :
    - `$numTra` : Le numéro de la traversée.
    - `$idType` : L'identifiant du type de billet.
- **Retour** : Le tarif correspondant ou `null` si aucun tarif n'est trouvé.

```php
function getTarifByType($numTra, $idType) { ... }
```

## Fonction `getPorts`

Récupère la liste de tous les ports disponibles dans la base de données.

### Détails

- **Requête** : Sélectionne tous les noms de ports triés par ordre alphabétique.
- **Paramètres** : Aucun
- **Retour** : Un tableau des noms de ports ou `null` si aucun port n'est trouvé.

```php
function getPorts() { ... }
```

## Fonction `getTarifsByNumTra`

Récupère tous les tarifs associés à une traversée donnée.

### Détails

- **Requête** : Sélectionne tous les tarifs pour chaque type de billet d'une traversée.
- **Paramètres** :
    - `$numTra` : Le numéro de la traversée.
- **Retour** : Un tableau associatif des tarifs indexé par l'ID du type.

```php
function getTarifsByNumTra($numTra) { ... }
```

## Fonction `getTempsTotalTraversee`

Calcule le temps total d'une traversée.

### Détails

- **Requête** : Récupère l'heure de départ et le temps de liaison pour calculer la durée totale.
- **Paramètres** :
    - `$numTra` : Le numéro de la traversée.
- **Retour** : Le temps total au format "XXhYY" ou `null` si erreur.

```php
function getTempsTotalTraversee($numTra) { ... }
```

## Fonction `getPrixMinimumPourTraversee`

Trouve le prix minimum parmi tous les types de billets pour une traversée.

### Détails

- **Logique** : Parcourt tous les types de billets (1 à 7) et compare leurs tarifs.
- **Paramètres** :
    - `$numTra` : Le numéro de la traversée.
- **Retour** : Le prix minimum trouvé ou `null` si aucun prix n'est disponible.

```php
function getPrixMinimumPourTraversee($numTra) { ... }
```

## Fonction `checkReservationExists`

Vérifie l'existence d'un numéro de réservation dans la base de données.

### Détails

- **Requête** : Vérifie si le numéro de réservation existe dans la table des réservations.
- **Paramètres** :
    - `$numRes` : Le numéro de réservation à vérifier.
- **Retour** : `true` si la réservation existe, `false` sinon.

```php
function checkReservationExists($numRes) { ... }
```

## Fonction `getInfosTraversee`

Récupère toutes les informations détaillées d'une traversée spécifique.

### Détails

- **Requête** : Sélectionne les informations complètes d'une traversée incluant le bateau, les ports et les détails de liaison.
- **Paramètres** :
    - `$numTra` : Le numéro de la traversée.
- **Retour** : Un tableau associatif contenant tous les détails de la traversée ou `null` si non trouvée.

```php
function getInfosTraversee($numTra) { ... }
```

## Sessions

Le fichier inclut également une gestion de session pour stocker le numéro de traversée sélectionné par l'utilisateur :

```php
session_start();
if (isset($_POST['numTra'])) {
    $_SESSION['numTra'] = $_POST['numTra'];
}
