import { API_BASE_URL, API_PATH } from "./config";
import { SoireeDetail } from './types';

export async function fetchSoireeDetail(id: string): Promise<SoireeDetail> {
  const response = await fetch(`/soirees/${id}`);
  if (!response.ok) {
    throw new Error('Impossible de charger le détail de la soirée');
  }
  return await response.json();
}

export async function fetchSoireeBySpectacleId(id: string): Promise<SoireeDetail> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}spectacle/${id}/soiree`);
  if (!response.ok) {
    throw new Error('Impossible de charger le détail de la soirée');
  }

  const data = await response.json() as SoireeDetailResponse;

  return data.soiree;
}