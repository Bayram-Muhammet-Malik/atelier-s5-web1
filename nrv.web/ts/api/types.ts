export interface Spectacle {
    id: string;
    titre: string;
    artistes: string;
    description: string;
    style: string;
    horaire: string;
    video: string | null;
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