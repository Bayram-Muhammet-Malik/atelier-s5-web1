# Scénario d'Usage : Achat d'un Pass 3 jours et Consultation de Billets sur NRV.net

## Acteurs
- **Jean** : Festivalier souhaitant assister à plusieurs soirées du festival Nancy Rock Vibration avec des amis.
- **Système (NRV.net)** : L'application web de billetterie (Front-end web/mobile + API REST Backend).
- **Contrôleur d'accès** : Le staff à l'entrée de la salle de concert.

## Préquations / Contexte
- Le festival se déroule sur deux semaines.
- Jean navigue sur son smartphone.
- Jean n'a pas encore de compte sur la plateforme.

## Déroulement du Scénario

### Phase 1 : Découverte et Consultation du Programme
1. **Accès au site** : Jean arrive sur la page d'accueil de NRV.net depuis son smartphone.
2. **Consultation de la liste des spectacles** :
   - Jean parcourt la liste chronologique des spectacles avec leurs titres, images, dates et horaires.
   - Il applique un filtre par **style de musique** pour afficher uniquement les soirées *Blues Rock* et *Métal*.
3. **Consultation du détail d'une soirée** :
   - Jean clique sur un spectacle qui l'intéresse (« Nuit du blues »).
   - L'application affiche le détail complet de la soirée : le nom de la soirée, sa thématique, la date, l'horaire de début, le lieu (ex: *Salle Poirel*), le nombre de places restantes, ainsi que la liste des 3 artistes/spectacles prévus avec leurs vidéos d'illustration.

### Phase 2 : Sélection des Billets et Panier
4. **Ajout au panier** :
   - Jean souhaite venir avec un ami. Depuis la fiche de la soirée, il sélectionne **2 billets en tarif Normal** et les ajoute à son panier.
   - Il continue sa navigation et découvre l'offre **Pass 3 jours** (bénéficiant d'une remise de 3%) il modifie alors sont panier pour ajouter une soirée supplémentaire pour profiter de ce pass.
5. **Visualisation du panier** :
   - Jean clique sur l'icône du panier.
   - L'écran affiche le récapitulatif des 3 soirées choisies, le détail du type de tarif (*Pass 3 jours*), le calcul automatique de la remise et le montant total à régler.
   - Il ajuste la quantité d'un des billets puis valide son panier.

### Phase 3 : Authentification et Passation de Commande
6. **Inscription / Connexion** :
   - Le système indique que la validation nécessite un compte client.
   - Jean crée son compte en renseignant son nom, prénom, e-mail et mot de passe.
   - Une fois authentifié, un jeton JWT est attribué à sa session.
7. **Validation de la commande** :
   - Le panier est transformé en commande au statut `en_attente`.

### Phase 4 : Paiement et Génération des Billets
8. **Paiement sécurisé (Simulation)** :
   - Jean est redirigé vers la page de paiement. Il saisit les coordonnées d'une carte bancaire de test (numéro, date d'expiration, cryptogramme).
   - À la validation du paiement :
     1. Le système vérifie une dernière fois la disponibilité des places pour chaque soirée sélectionnée.
     2. Le quota des places disponibles pour chaque soirée est décrémenté.
     3. La commande passe au statut `payee`.
9. **Génération des billets et accès par Token** :
   - Le système génère les billets correspondants.
   - Chaque billet intègre une référence unique, le nom d'acheteur, la catégorie de tarif et un **token aléatoire sécurisé** unique dans son URL.

### Phase 5 : Consultation et Impression des Billets
10. **Consultation dans l'espace personnel** :
   - Connecté à son espace client, Jean se rend sur la page `/billets` (`GET /billets` avec son token JWT). Il y retrouve la liste complète de ses billets et Pass achetés.
11. **Partage à son ami (Accès anonyme par Token)** :
   - Jean copie l'URL directe contenant le token unique d'un des billets (`/billets/cae00dabed158bde52be071ac7b3d7ca`) et l'envoie par SMS à son ami.
   - Son ami clique sur le lien depuis son téléphone : le billet s'affiche instantanément **sans avoir besoin de se connecter ni de créer de compte**.
12. **Impression / Affichage HTML** :
   - L'affichage du billet est optimisé pour les écrans mobiles et prêt à être imprimé proprement (via les styles `@media print`).

### Phase 6 : Jour du Concert (Accès à la salle)
13. **Contrôle à l'entrée** :
   - Le soir du concert, Jean et son ami présentent leur smartphone à l'entrée du festival.
   - Le contrôleur scanne/vérifie le lien ou le token présent sur le billet pour valider leur accès à la soirée.