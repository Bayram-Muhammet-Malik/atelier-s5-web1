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
}

export interface SoireeDetail {
    id: string;
    nom: string;
    thematique: string;
    date: string;
    horaire: string;
    lieu: string;
    tarifNormal: number;
    tarifReduit: number;
    spectacles: Spectacle[];
}

export interface SoireeResponse {
    soiree: SoireeDetail;
}
