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
- Executer les requètes SQL des fichiers ``sql/nrv.schema.sql`` puis ``sql/nrv.data.sql`` dans adminer (http://localhost:16800 par defaut)
- Une fois les fichiers complétés avec la bonne configuration
- Lancer docker (s'il n'est pas déjà lancé) puis ouvrez un terminal dans le repertoire racine du projet giftbox et excecutez la commande suivante : ``docker compose up``
- Acceder à l'appli via http://localhost:16797 et http://localhost:16798

### Fonctionnalité réalisées
- Muhammet :
    - Maquettes
    - Backend
        - 1 (Affichage de la liste des spectacles : pour chaque spectacle, on affiche le titre, la date et  l’horaire, une image.)
        - 4 (Filtrage de la liste des spectacles par date.)
        - 5 (Filtrage de la liste des spectacles par style de musique.)
    - Frontend
        - 7 (Visualisation de l’état courant du panier, calcul et affichage du montant total.)
- Loïc :
    - Maquettes
    - Backend
        - 2 (Affichage du détail d’une soirée : nom de la soirée, thématique, date et horaire, lieu, tarifs,  ainsi que la liste des spectacles : titre, artistes, description, style de musique, vidéo.)
        - Système de login/inscription
    - Frontend
        - Système de login/inscription
        - 6 (Lors de la visualisation d’une soirée, possibilité d’ajouter des billets d’entrée pour cette soirée dans un panier.)
        - 8 (Validation du panier et transformation en commande.)
- Johan :
    - Backend
        - 10 (Création des billets qui doivent comporter : nom de l’acheteur, référence, date et horaire de la soirée, catégorie de tarif; les billets doivent être imprimables et peuvent être réalisés en html/css avec @media print.)
        - 11 (Affichage de la jauge des spectacles: nombre de places vendues pour chaque soirée, recette de la soirée.)
        - Tests fonctionnalités 1 2 3
        - Système de login/inscription
        - 8 (Validation du panier et transformation en commande.) 
        - 9 (Paiement de la commande : on simule en transmettant des données correspondant à une carte bancaire : n°, date expiration, code ; le contrôle de disponibilité des places est réalisé à ce moment-là, ainsi que la mise à jour du nombre de places disponibles pour la soirée. C’est à ce moment que la commande est validée.)
    - Frontend
        - 10 (Création des billets qui doivent comporter : nom de l’acheteur, référence, date et horaire de la soirée, catégorie de tarif; les billets doivent être imprimables et peuvent être réalisés en html/css avec @media print.)
- Léo :
    - Maquettes
    - Backend
        - 3 (En cliquant sur un spectacle dans la liste, le détail de la soirée correspondante est affiché.)
        - E1 (Pagination de la liste de spectacles.)
        - Pass 3J
    - Frontend
        - 1 (Affichage de la liste des spectacles : pour chaque spectacle, on affiche le titre, la date et  l’horaire, une image.)
        - 2 (Affichage du détail d’une soirée : nom de la soirée, thématique, date et horaire, lieu, tarifs,  ainsi que la liste des spectacles : titre, artistes, description, style de musique, vidéo.)
        - 3 (En cliquant sur un spectacle dans la liste, le détail de la soirée correspondante est affiché.)
        - 4 (Filtrage de la liste des spectacles par date.)
        - 5 (Filtrage de la liste des spectacles par style de musique.)
        - E1 (Pagination de la liste de spectacles.)
        - Système de login/inscription
        - Pass 3J
        - (Affichage de la jauge des spectacles: nombre de places vendues pour chaque soirée, recette de la soirée.)
        - 10 (Création des billets qui doivent comporter : nom de l’acheteur, référence, date et horaire de la soirée, catégorie de tarif; les billets doivent être imprimables et peuvent être réalisés en html/css avec @media print.)
        - 9 (Paiement de la commande : on simule en transmettant des données correspondant à une carte bancaire : n°, date expiration, code ; le contrôle de disponibilité des places est réalisé à ce moment-là, ainsi que la mise à jour du nombre de places disponibles pour la soirée. C’est à ce moment que la commande est validée.)

### Comptes utilisateurs de démo
- Mail ``admin@nrv.net``, mdp `AdM1n1&tr4t0R`, rôle : Administrateur
- Mail ``malik.bayram@mail.fr``, mdp `Malik1234`, rôle : Client
- Mail ``loic.durand@mail.fr``, mdp `Loic1234`, rôle : Client
- Mail ``johan.schaeffer@mail.fr``, mdp `Johan1234`, rôle : Client
- Mail ``leo.thomas@mail.fr``, mdp `Leo1234`, rôle : Client

### Liste des routes de l'API
#### Global
- ``GET`` - ``/soirees`` : Voir la liste des soirées
- ``GET`` - ``/soirees/{id}`` : Voir le détail d'une soirée
- ``GET`` -  ``/spectacles`` : Voir la liste des spéctacles
- ``GET`` -  ``/spectacles/{id}/soiree`` : Voir le détail d'une soirée associé à l'id du spéctacle
- ``GET`` -  ``/styles`` : Voir la liste des styles de musiques
- ```GET`` -  ``/dates`` : Date du premier spéctacle et date du dernier spéctacle enregistré en base de donnée

#### Espace utilisateur
- ``GET`` - ``/billets`` : Liste des billets payé de l'utilisateur **(doit être connecté)**
- ``GET`` - ``/billets/{token}`` : Voir le billet associer au token
- ``POST`` -  ``/login`` : Se connecter
- ``POST`` -  ``/signup`` : Créer un compte
- ``POST`` -  ``/commandes`` : Créer une commande à partir du panier **(doit être connecté)**
- ``POST`` -  ``/commandes/{id}/paiement`` : Marqué une commande comme payé **(doit être connecté)**

#### Backoffice (espace Admin)
- ``GET`` - ``/admin/jauge`` : Jauge du panneau principale du backoffice **(doit être connecté + administrateur)**