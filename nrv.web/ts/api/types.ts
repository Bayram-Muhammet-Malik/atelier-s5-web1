export interface Spectacle {
    id: string;
    titre: string;
    artistes: string;
    description: string;
    style: string;
    dateSoiree: string | null;
    horaire: string;
    video: string | null;
    image: string | null;
}

export interface SpectaclesResponse {
    spectacles: {
        spectacle: Spectacle;
        links: {
            soiree: {
                href: string;
            };
        };
    }[];
    links: {
        prev?: {
            href: string;
        };
        next?: {
            href: string;
        };
    }
}

export interface SpectaclesResult {
    spectacles: Spectacle[];
    hasPreviousPage: boolean;
    hasNextPage: boolean;
}

export interface Soiree {
    id: string;
    nom: string;
    thematique: string;
    date: string;
    horaire: string;
    placesRestantes: number;
    lieu: string;
}

export interface SoireesResponse {
    soirees: {
        soiree: Soiree;
    }[];
}

export interface SoireeDetail {
    id: string;
    nom: string;
    thematique: string;
    date: string;
    horaire: string;
    placesRestantes: number;
    lieu: string;
    tarifNormal: number;
    tarifReduit: number;
    spectacles: Spectacle[];
}

export interface SoireeResponse {
    soiree: SoireeDetail;
}

export interface Style {
    id: string;
    libelle: string;
}

export interface StyleResponse {
    styles: {
        style: Style;
    }[];
}

export interface AuthResponse {
  token: string;
  message?: string;
  error?: string;
}

export interface Jauge {
    idSoiree: string;
    nomSoiree: string;
    dateSoiree: string;
    capacite: number;
    placesVendues: number;
    placesRestantes: number;
    recette: number;
}

export interface JaugesResponse {
    soirees: {
        jauge: Jauge;
        links: {
        soiree: {
            href: string;
        };
    };
    }[];
    links: {
        self: {
            href: string;
        };
    };
}

export interface BilletSoiree {
    idSoiree: string;
    nomSoiree: string;
    lieu: string;
    dateSoiree: string;
    heureDebut: string;
}

export interface Billet {
    reference: string;
    nomAcheteur: string;
    idSoiree: string;
    nomSoiree: string;
    lieu: string;
    dateSoiree: string;
    heureDebut: string;
    categorieTarif: string;
    prixPaye: number;
    estPass: boolean;
    soirees?: BilletSoiree[];
}

export interface BilletResponse {
    billet: Billet;
}
