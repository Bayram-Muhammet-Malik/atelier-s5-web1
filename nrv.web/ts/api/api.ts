import { API_BASE_URL, API_PATH } from "./config";
import { SoireeDetail, SoireeResponse, Spectacle, SpectaclesResponse, Style, StylesResponse } from './types';

export async function fetchSoireeDetail(id: string): Promise<SoireeDetail> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}soirees/${id}`);
  if (!response.ok) throw new Error('Impossible de charger le détail de la soirée');

  const data = await response.json() as SoireeResponse;

  data.soiree.spectacles.forEach((spectacle) => {
    if (spectacle.image && !spectacle.image.startsWith("http")) spectacle.image = `${API_BASE_URL}${API_PATH}${spectacle.image}`;
    if (spectacle.video && !spectacle.video.startsWith("http")) spectacle.video = `${API_BASE_URL}${API_PATH}${spectacle.video}`;
  });

  return data.soiree;
}

export async function fetchSoireeBySpectacleId(id: string): Promise<SoireeDetail> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}spectacles/${id}/soiree`);
  if (!response.ok) throw new Error('Impossible de charger le détail de la soirée');

  const data = await response.json() as SoireeResponse;

  data.soiree.spectacles.forEach((spectacle) => {
    if (spectacle.image && !spectacle.image.startsWith("http")) spectacle.image = `${API_BASE_URL}${API_PATH}${spectacle.image}`;
    if (spectacle.video && !spectacle.video.startsWith("http")) spectacle.video = `${API_BASE_URL}${API_PATH}${spectacle.video}`;
  });

  return data.soiree;
}

export async function fetchSpectacles(style?: string | null, date?: string | null): Promise<Spectacle[]> {
  const url = new URL(`${API_BASE_URL}${API_PATH}spectacles`);

  if (style) {
    url.searchParams.append('style', style);
  }
  if (date) {
    url.searchParams.append('date', date);
  }

  const response = await fetch(url.toString());

  if (!response.ok) throw new Error("Impossible de charger les spectacles");

  const data = await response.json() as SpectaclesResponse;

  return data.spectacles.map((item) => {
    const spectacle = item.spectacle;

    if (spectacle.image && !spectacle.image.startsWith("http")) spectacle.image = `${API_BASE_URL}${API_PATH}${spectacle.image}`;
    if (spectacle.video && !spectacle.video.startsWith("http")) spectacle.video = `${API_BASE_URL}${API_PATH}${spectacle.video}`;

    return spectacle;
  });
}

export async function fetchStyles(): Promise<Style[]> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}styles`);

  if (!response.ok) throw new Error("Impossible de charger les styles");

  const data = await response.json() as StyleResponse;
  return data.styles; 
}