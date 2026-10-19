import { SoireeDetail } from './types';

export async function fetchSoireeDetail(id: string): Promise<SoireeDetail> {
  const response = await fetch(`/api/soirees/${id}`);
  if (!response.ok) {
    throw new Error('Impossible de charger le détail de la soirée');
  }
  return await response.json();
}