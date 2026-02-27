# Calendar-BY-Sofiane
Here is the calendar that I had to code as homework using php html and css . 

# Fonctionnalités
Affichage : Vue mensuelle avec navigation (Mois précédent / Mois suivant).

Indicateurs visuels : Les jours contenant des événements sont marqués visuellement sur le calendrier.

CRUD complet :

Création d'événements.

Lecture (Liste filtrée par mois).

Mise à jour (Modification).

Suppression.

Gestion Utilisateur : Système d'identification automatique via Cookie (pas d'inscription nécessaire).

Sécurité : Protection contre les injections SQL et les failles XSS.

MySQL.

# Installation
Fichiers : Placez le dossier du projet dans le répertoire racine de votre serveur web

Base de données :

Ouvrez PHPMyAdmin.

Créez une nouvelle base de données nommée db_calendar.

Importez le fichier db_calendar.sql fourni.

Configuration :

Le fichier index.php est configuré par défaut pour un utilisateur root avec le mot de passe root.

Si nécessaire, modifiez les variables $root et $password au début du fichier index.php (ligne 33) pour correspondre à votre configuration locale. (je suis sur mac)


# Choix Techniques & Sécurité
1. Gestion des Utilisateurs (Cookies)
Conformément à la consigne, il n'y a pas de système de "Login/Mot de passe".

À la première visite, un identifiant aléatoire unique (ex: 849203948) est généré.

Cet ID est stocké dans un Cookie (user_id) valable 1 an.

Chaque événement créé est associé à cet ID dans la base de données.

2. Sécurité des Données
Isolation : Un utilisateur ne peut modifier ou supprimer que ses propres événements. Une vérification est effectuée à chaque requête UPDATE ou DELETE (WHERE user_id = :uid).

Injections SQL

Failles XSS : Toutes les données affichées (nom de l'événement) sont protégées par la fonction htmlspecialchars() pour empêcher l'exécution de scripts malveillants.

3. Architecture
Le projet tient sur un fichier principal index.php pour la logique (traitement du formulaire en haut, affichage en bas) et un fichier style.css pour l'habillage graphique.

Auteurs : sofiane Lamrani
Date : 27 fevrier 2026
