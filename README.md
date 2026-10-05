# Médiathèque — bachelor_MVC

Application web de gestion d'une médiathèque (livres, films, albums) développée en PHP natif selon une architecture **MVC maison**, dans le cadre d'un projet d'école (Bachelor).

Elle permet de consulter, rechercher et trier le catalogue de médias publiquement, et, une fois authentifié, d'ajouter/modifier/supprimer des médias, de gérer leurs illustrations et de suivre les emprunts/retours depuis un tableau de bord.

## Stack technique

| Couche | Technologie |
|---|---|
| Langage | PHP 8.1+ (testé en 8.4), aucun framework |
| Architecture | MVC "maison" avec routeur/contrôleur frontal (`index.php`) |
| Base de données | MySQL / MariaDB, accès via **PDO** (requêtes préparées, pas d'ORM) |
| Authentification | Sessions PHP natives (`$_SESSION`), mots de passe hachés en `PASSWORD_ARGON2ID` |
| Frontend | Vues PHP côté serveur (pas de moteur de templating), CSS custom, JavaScript vanilla |
| Polices | Google Fonts — Fraunces (titres) & Inter (texte) |
| Dépendances | Aucune — pas de `composer.json` ni `package.json`, 100% PHP/JS/CSS natifs |

## Fonctionnalités

- **Médiathèque publique** : liste des médias (livres, films, albums), recherche approximative (tolérante aux fautes de frappe via distance de Levenshtein) sur le titre/auteur, tri par titre/auteur/disponibilité.
- **Authentification** : inscription, connexion, déconnexion. Mot de passe soumis à une politique de sécurité (8 caractères min., majuscule, minuscule, chiffre, caractère spécial, ne contenant pas le nom d'utilisateur).
- **Gestion des médias** (réservée aux utilisateurs connectés) :
  - Ajout / modification / suppression de livres, films et albums, chacun avec ses champs spécifiques (nombre de pages, durée + genre, nombre de pistes + éditeur).
  - Upload d'une illustration par média (JPEG/PNG/WEBP/GIF, 2 Mo max), stockée dans `uploads/` et tracée dans la table `Files` (voir [Gestion des fichiers](#gestion-des-fichiers)).
  - Emprunt / retour d'un média, avec mise à jour de la disponibilité.
- **Tableau de bord** : statistiques (total, disponibles, empruntés) et liste tabulaire des médias.
- **Messages flash** : confirmation/erreur affichés après une action puis effacés (auto-disparition côté JS après quelques secondes).

## Structure du projet

```
bachelor_MVC/
├── index.php                  # Contrôleur frontal / routeur
├── controllers/
│   ├── MediaController.php    # CRUD médias, upload illustration, emprunt/retour
│   └── UserController.php     # Inscription, connexion, déconnexion
├── models/
│   ├── Media.php               # Classe abstraite commune (Book/Movie/Album)
│   ├── File.php                # Fichiers uploadés (table Files) : validation, stockage, suppression
│   ├── Book.php / Movie.php / Album.php
│   └── User.php
├── views/
│   ├── partials/               # header.php / footer.php
│   ├── media/                  # add.php / edit.php
│   ├── user/                   # login.php / signin.php
│   ├── library.php             # Médiathèque publique
│   ├── dashboard.php           # Tableau de bord
│   └── 404.html
├── uploads/                    # Illustrations uploadées (contenu ignoré par git)
├── includes/
│   ├── db_connect.php          # Connexion PDO
│   ├── auth.php                # isAuthenticated() / requireAuth()
│   └── flash.php               # Messages flash en session
└── assets/
    ├── css/style.css
    └── js/app.js
```

## Routage

Il n'y a pas de réécriture d'URL (`.htaccess` vide) : le routage se fait via le paramètre `action` de `index.php`, au format `Contrôleur/méthode/param1/param2`.

```
index.php?action=Media/library                    → liste (défaut si "action" absent)
index.php?action=Media/library/title/asc&q=...     → tri + recherche
index.php?action=Media/add/{book|movie|album}      → formulaire d'ajout   [connecté]
index.php?action=Media/update/{id}                  → formulaire d'édition [connecté]
index.php?action=Media/delete/{id}                   → suppression         [connecté]
index.php?action=Media/borrow/{id}                    → emprunt              [connecté]
index.php?action=Media/giveBack/{id}                    → retour                [connecté]
index.php?action=Media/dashboard                        → tableau de bord      [connecté]
index.php?action=User/signin                              → inscription
index.php?action=User/login                                 → connexion
index.php?action=User/logout                                 → déconnexion
```

`{Contrôleur}Controller` doit exister dans `controllers/` et exposer une méthode statique `{méthode}` ; à défaut, la page `views/404.html` est renvoyée.

## Base de données

Base `bachelorMVC`. Le schéma complet (tables, clés étrangères, contraintes) est disponible dans [`bachelorMVC.sql`](./bachelorMVC.sql) à la racine du projet.

## Gestion des fichiers

Chaque illustration est enregistrée dans la table `Files` :

| Colonne | Rôle |
|---|---|
| `id` | Identifiant du fichier |
| `media_id` | Média rattaché (`ON DELETE CASCADE`) |
| `original_name` | Nom du fichier envoyé par l'utilisateur (affichage uniquement) |
| `stored_name` | Nom unique généré côté serveur (`random_bytes`), utilisé sur le disque |
| `size` | Taille en octets |
| `mime_type` | Type MIME détecté côté serveur |
| `uploaded_at` | Date d'envoi |
| `uploaded_by` | Utilisateur ayant envoyé le fichier (`ON DELETE SET NULL`) |

Vérifications côté serveur (`File::validateUpload`) : erreur d'envoi, taille réelle ≤ 2 Mo, type MIME lu dans le contenu du fichier (`finfo`, jamais l'extension ni le type annoncé par le navigateur) et image décodable (`getimagesize`). L'extension du fichier stocké est déduite du type détecté.

Le fichier est validé **avant** toute écriture en base. Lors d'un remplacement, l'ancienne illustration n'est supprimée (base + disque) qu'une fois la nouvelle enregistrée. La suppression d'un média supprime aussi son fichier.

## Installation

### Prérequis

- PHP 8.1 ou supérieur, avec les extensions `pdo_mysql`, `mbstring` et `fileinfo`
- MySQL ou MariaDB
- Un serveur web (Apache, Nginx, ou le serveur intégré de PHP)

### Étapes

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-depot>
   cd bachelor_MVC
   ```

2. **Créer la base de données** en important le dump `bachelorMVC.sql` sur votre serveur MySQL/MariaDB. Sur une base créée avant l'ajout de la table `Files`, exécutez plutôt `migrations/001_create_files.sql`.

3. **Configurer la connexion** dans `includes/db_connect.php` (hôte, utilisateur, mot de passe, nom de la base) selon votre environnement local.

4. **Rendre le dossier d'uploads accessible en écriture**
   ```bash
   chmod -R 755 uploads
   ```

5. **Lancer le serveur** depuis la racine du projet (celle-ci doit être la racine web, `index.php` compris) :
   ```bash
   php -S localhost:8000
   ```

6. **Ouvrir l'application** : http://localhost:8000/

7. **Se connecter** : pour accéder aux fonctionnalités réservées (ajout/modification/suppression, emprunt, tableau de bord), créez votre propre compte via la page d'inscription (`index.php?action=User/signin`).
