import { API_BASE_URL, API_PATH } from "./config";
import { getAuthHeaders } from './auth';
import { Soiree, SoireesResponse, SoireeDetail, SoireeResponse, Spectacle, SpectaclesResponse, Style, StylesResponse, AuthResponse } from './types';

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
  const response = await fetch(`${API_BASE_URL}${API_PATH}styles`);

  if (!response.ok) throw new Error("Impossible de charger les styles");

  const data = await response.json() as StylesResponse;

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