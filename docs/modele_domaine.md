# Modèle du domaine métier

L’application permet de gérer un festival de spectacles :
- des soirées se déroulent dans des lieux
- chaque soirée contient des spectacles
- les spectacles sont associés à des artistes et à des styles
- les utilisateurs peuvent acheter des billets
- les billets sont regroupés dans des commandes
- le paiement vérifie les places restantes.

![Modèle du domaine](./modele_domaine.png)

```puml
@startuml
title Modèle du domaine métier - NRV

class Utilisateur {
    - id : string
    - nom : string
    - prenom : string
    - email : string
    - motDePasse : string
    - role : int
    --
    + isAdmin() : bool
}

class Lieu {
    - id : string
    - nom : string
    - adresse : string
    - nbPlaces : int
}

class Soiree {
    - id : string
    - nom : string
    - thematique : string
    - dateHeureDebut : DateTime
    - placesRestantes : int
}

class Spectacle {
    - id : string
    - titre : string
    - description : string?
    - urlVideo : string?
    - dateSoiree : string?
    - horaire : string?
}

class Artiste {
    - id : string
    - nom : string
}

class Style {
    - id : string
    - libelle : string
}

class Image {
    - id : string
    - url : string
}

class Tarif {
    - id : string
    - libelle : string
    - prix : float
}

class Billet {
    - id : string
    - reference : string
    - token : string
    - prixPaye : float
    --
    + isPass() : bool
}

class Commande {
    - id : string
    - dateCommande : DateTime
    - montantTotal : float
    - statut : string
    --
    + payer() : void
}

class JaugeSoiree {
    - placesVendues : int
    - recette : float
}

Lieu "1" <-- "0..*" Soiree : se déroule dans

Soiree "1" *-- "0..*" Spectacle : contient

Spectacle "0..*" --> "1" Style : possède

Spectacle "0..*" -- "0..*" Artiste : présente

Spectacle "1" *-- "0..*" Image : possède

Utilisateur "1" <-- "0..*" Commande : passe

Commande "1" *-- "1..*" Billet : contient

Billet "0..*" --> "1" Tarif : utilise

Billet "0..*" --> "0..1" Utilisateur : acheté par

Billet "0..*" -- "1..*" Soiree : concerne

JaugeSoiree "1" --> "1" Soiree : mesure

@enduml
```