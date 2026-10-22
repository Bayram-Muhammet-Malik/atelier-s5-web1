# atelier-s5-web1
- Muhammet BAYRAM
- Loïc DURAND
- Johan SCHAEFFER
- Léo THOMAS

S5DWM2

### Ressources
- Dépot Git : https://github.com/Bayram-Muhammet-Malik/atelier-s5-web1
- URL Docketu (Frontend) : http://docketu.iutnc.univ-lorraine.fr:16797/
- URL Docketu (API) : http://docketu.iutnc.univ-lorraine.fr:16798/

### Installation
- Copier-coller le fichier ``.env.dist`` en ``.env`` et remplir le fichier
- Faire de même pour ``nrv.core/config/.env.dist`` en ``nrv.core/config/.env``
- Executer les requètes SQL des fichiers ``sql/SAE_tables.sql`` puis ``sql/donnees.sql`` dans adminer (http://localhost:16800 par defaut)
- Une fois les fichiers complétés avec la bonne configuration
- Lancer docker (s'il n'est pas déjà lancé) puis ouvrez un terminal dans le repertoire racine du projet giftbox et excecutez la commande suivante : ``docker compose up``
- Acceder à l'appli via http://localhost:16797 et http://localhost:16798

### Fonctionnalité réalisées
- Muhammet :
    - Backend (Panel Admin)
        - 1 (Créer un article : affichage et traitement d’un formulaire de saisie d’un article)
        - 3 (Afficher la liste des articles : affichage de la liste des articles sur le CMS)
        - 10 (Api : liste des articles disponible au format JSON sur l’url /api/articles)
        - 11 (Api : articles d’une catégorie disponible sur l’url /api/categories/{id_categ}/articles.)
        - 16 (Tri de la liste d’articles dans l’api)
    - Frontend (Site static)
        - 5 (Affichage liste articles d'un auteur en cliquant sur son nom)
- Loïc :
    - Backend (Panel Admin)
        - 1 (Créer un article : affichage et traitement d’un formulaire de saisie d’un article)
        - 2 (Créer un article en choisissant une catégorie)
        - 3 (Afficher la liste des articles : affichage de la liste des articles sur le CMS)
        - 4 (Afficher la liste des articles en filtrant par catégorie)
        - 13 (Api : liste des articles d’un auteur disponible à l’url /api/auteurs/{id}/articles)
    - Frontend (Site static)
        - 2 (Affichage complet d’un article en cliquant sur un article dans une liste)
        - 6 (Tri des listes d’articles selon l’ordre ascendant ou descendant de la date de création)
- Johan :
    - Backend (Panel Admin)
        - 5 (Création d’une catégorie)
        - 6 (Formulaire d’authentification)
        - 9 (Api : liste des catégories disponible au format JSON sur l’url /api/categories)
        - 12 (Api : article complet disponible au format JSON à l’url /api/articles/{id_a})
        - 15 (Création d’utilisateurs)
    - Frontend (Site static)
        - 1 (Affichage de la liste des articles dans l’ordre chronologique inverse)
        - 3 (Affichage de la liste des articles d’une catégorie, en cliquant sur la catégorie)
        - 7 (Filtrage des listes d’articles selon un mot clé dans le titre)
        - 8 (Filtrage des listes d’articles selon un mot clé dans le titre ou dans le résumé)
- Léo :
    - Backend (Panel Admin)
        - 7 (Contrôle d’accès)
        - 8 (Auteurs : affichage de l'auteur de chaque article dans la liste d'article)
        - 14 (Publication/dépublication des articles)
        - 15 (Création d’utilisateurs)
        - 16 (Tri de la liste d’articles dans l’api)
    - Frontend (Site static)
        - 1 (Affichage de la liste des articles dans l’ordre chronologique inverse)
        - 4 (Affichage complet d’un article en cliquant sur un article dans une liste)
        - 5 (Affichage liste articles d'un auteur en cliquant sur son nom)

### Comptes utilisateurs de démo
- Mail ``admin@nrv.net``, mdp `AdM1n1&tr4t0R`, rôle : Administrateur
- Mail ``malik.bayram@mail.fr``, mdp `Malik1234`, rôle : Client
- Mail ``loic.durand@mail.fr``, mdp `Loic1234`, rôle : Client
- Mail ``johan.schaeffer@mail.fr``, mdp `Johan1234`, rôle : Client
- Mail ``leo.thomas@mail.fr``, mdp `Leo1234`, rôle : Client

| Permission | **Auteur (>= 1 && < 50)** | **Admin (>= 50 && < 100)** | **SuperAdmin (100)** |
|-----------|-------------------------------|----------------------------------|-------------------------------------------|
| **Comptes pour tester** | Mail `loic@gmail.com`, mdp `Loic1234` | Mail `hi@gmail.com`, mdp `Malik1234`<br>Mail `johan@gmail.com`, mdp `Johan1234` | Mail `leo.thomas@minipress.com`, mdp `Utilisateur1` |
| **Créer un article** | Oui | Oui | Oui |
| **Créer une catégorie** | Oui | Oui | Oui |
| **Voir les articles** | Oui | Oui | Oui |
| **Publier/dépublier des articles** | Uniquement les siens | Tous | Tous |
| **Créer des comptes utilisateurs** | Non | Non | Oui |