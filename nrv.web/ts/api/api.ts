import { API_BASE_URL, API_PATH } from "./config";
import { getAuthHeaders } from './auth';
import { Billet, BilletResponse, Soiree, SoireesResponse, SoireeDetail, SoireeResponse, SpectaclesResponse, Style, StyleResponse, Jauge, JaugesResponse, MyBilletsResponse } from './types';
import type { CartItem } from "./cart";

export interface CommandeCreatedResponse {
    commande: {
        id: string;
        [key: string]: unknown;
    };
    links?: {
        paiement?: {
            href: string;
        };
    };
}

export interface CommandeApiResponse {
    commande: {
        id: string;
        [key: string]: unknown;
    };
    links?: {
        paiement?: {
            href: string;
        };
        billets?: {
            href: string;
        }[];
    };
}

async function lireErreurApi(
    response: Response,
    messageParDefaut: string
): Promise<Error> {
    const data: unknown = await response.json().catch(() => null);

    if (data && typeof data === "object") {
        const obj = data as Record<string, unknown>;

        if (typeof obj.message === "string") {
            return new Error(obj.message);
        }

        if (typeof obj.error === "string") {
            return new Error(obj.error);
        }
    }

    return new Error(messageParDefaut);
}

/**
 * Crée une commande à partir des lignes du panier.
 * Les prix sont recalculés par le backend.
 */
export async function creerCommande(
    lignes: CartItem[]
): Promise<CommandeApiResponse> {
    if (lignes.length === 0) {
        throw new Error("Le panier est vide.");
    }

    const response = await fetch(
        `${API_BASE_URL}${API_PATH}commandes`,
        {
            method: "POST",
            headers: getAuthHeaders(),
            body: JSON.stringify({
                lignes: lignes.map(item => ({
                    soireeId: String(item.soireeId),
                    tarifType: item.tarifType,
                    quantite: item.quantite
                }))
            })
        }
    );

    if (!response.ok) {
        throw await lireErreurApi(
            response,
            "Impossible de créer la commande."
        );
    }

    const data = await response.json() as CommandeApiResponse;

    if (!data.commande || !data.commande.id) {
        throw new Error(
            "Le backend n'a pas retourné l'identifiant de la commande."
        );
    }

    return data;
}

/**
 * Paie une commande avec la simulation de carte.
 */
export async function payerCommande(
    idCommande: string,
    carte: {
        numero: string;
        code: string;
        expiration: string;
    }
): Promise<CommandeApiResponse> {
    const response = await fetch(
        `${API_BASE_URL}${API_PATH}commandes/${encodeURIComponent(idCommande)}/paiement`,
        {
            method: "POST",
            headers: getAuthHeaders(),
            body: JSON.stringify(carte)
        }
    );

    if (!response.ok) {
        throw await lireErreurApi(
            response,
            "Le paiement a échoué."
        );
    }

    return await response.json() as CommandeApiResponse;
}


function formatMediaUrl(url: string | null): string | null {
  if (!url) return null;
  if (url.startsWith("http")) return url;

  const cleanPath = url.startsWith("/") ? url : `/${url}`;
  return `${API_BASE_URL}${cleanPath}`;
}

export async function fetchSoirees(): Promise<Soiree[]> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}soirees`);

  if (!response.ok) throw new Error('Impossible de charger les soirées');

  const data = await response.json() as SoireesResponse;

  return data.soirees;
}

export async function fetchSoireeDetail(id: string): Promise<SoireeDetail> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}soirees/${id}`);
  if (!response.ok) throw new Error('Impossible de charger le détail de la soirée');

  const data = await response.json() as SoireeResponse;

  data.soiree.spectacles.forEach((spectacle) => {
    spectacle.image = formatMediaUrl(spectacle.image);
    spectacle.video = formatMediaUrl(spectacle.video);
  });

  return data.soiree;
}

export async function fetchSoireeBySpectacleId(id: string): Promise<SoireeDetail> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}spectacles/${id}/soiree`);
  if (!response.ok) throw new Error('Impossible de charger le détail de la soirée');

  const data = await response.json() as SoireeResponse;

  data.soiree.spectacles.forEach((spectacle) => {
    spectacle.image = formatMediaUrl(spectacle.image);
    spectacle.video = formatMediaUrl(spectacle.video);
  });

  return data.soiree;
}

export async function fetchSpectacles(style?: string | null, date?: string | null, limit?: number, offset?: number,): Promise<SpectaclesResult> {
  const url = new URL(`${API_BASE_URL}${API_PATH}spectacles`);

  if (style) url.searchParams.append("style", style);
  if (date) url.searchParams.append("date", date);
  if (limit !== undefined) url.searchParams.append("limit", String(limit));
  if (offset !== undefined) url.searchParams.append("offset", String(offset));

  const response = await fetch(url.toString());

  if (!response.ok) throw new Error("Impossible de charger les spectacles");

  const data = await response.json() as SpectaclesResponse;

  const spectacles = data.spectacles.map((item) => {
    const spectacle = item.spectacle;

    spectacle.image = formatMediaUrl(spectacle.image);
    spectacle.video = formatMediaUrl(spectacle.video);

    return spectacle;
  });

  return {
    spectacles,
    hasPreviousPage: Boolean(data.links.prev),
    hasNextPage: Boolean(data.links.next),
  };
}

export async function fetchStyles(): Promise<Style[]> {
    const response = await fetch(
        `${API_BASE_URL}${API_PATH}styles`
    );

    if (!response.ok) {
        throw new Error("Impossible de charger les styles");
    }

    const data = await response.json() as StyleResponse;

    return data.styles;
}

export async function fetchMinMaxDates(): Promise<{ min: string; max: string }> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}dates`);

  if (!response.ok) throw new Error("Impossible de charger les dates min et max");

  const data = await response.json();
  return data.date_limits;
}

export async function fetchJauges(): Promise<Jauge[]> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}admin/jauge`, {
    method: "GET",
    headers: getAuthHeaders()
  });

  if (!response.ok) {
    if (response.status === 401) throw new Error("Vous devez être connecté pour accéder à cette page.");
    if (response.status === 403) throw new Error("Accès réservé aux administrateurs.");
    throw new Error("Impossible de charger les jauges");
  }

  const data = await response.json() as JaugesResponse;
  return data.soirees;
}

export async function fetchBillet(token: string): Promise<Billet> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}billets/${token}`);

  if (!response.ok) throw new Error("Billet introuvable ou pas encore payé.");

  const data = await response.json() as BilletResponse;

  return data.billet;
}

export async function fetchMyBillets(): Promise<Billet[]> {
    const response = await fetch(`${API_BASE_URL}${API_PATH}billets`, {
        method: "GET",
        headers: getAuthHeaders()
    });

    if (!response.ok) {
        if (response.status === 401) throw new Error("Vous devez être connecté pour accéder à cette page.");
        throw new Error("Impossible de charger les billets");
    }

    const data = await response.json() as MyBilletsResponse;

    return data.billets.map(item => ({
        ...item.billet,
        soirees: item.billet.soirees.map((s: any) => s.soiree)
    }));
}