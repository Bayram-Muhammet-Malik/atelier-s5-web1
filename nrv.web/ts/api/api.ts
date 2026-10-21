import { API_BASE_URL, API_PATH } from "./config";
import { SoireeDetail, SoireeResponse, Spectacle, SpectaclesResponse, Style, StylesResponse } from './types';

function formatMediaUrl(url: string | null): string | null {
  if (!url) return null;
  if (url.startsWith("http")) return url;

  const cleanPath = url.startsWith("/") ? url : `/${url}`;
  return `${API_BASE_URL}${cleanPath}`;
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

  console.log(url.toString());

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

export function getAuthHeaders(): HeadersInit {
  const token = localStorage.getItem("auth_token");
  const headers: Record<string, string> = {
    "Content-Type": "application/json"
  };

  if (token) {
    headers["Authorization"] = `Bearer ${token}`;
  }

  return headers;
}