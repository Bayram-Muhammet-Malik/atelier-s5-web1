import { SoireeDetail, Spectacle } from './types';

function renderSpectacle(sp: Spectacle): string {
  return `
    <div class="spectacle-card">
      <h4>${sp.titre}</h4>
      <p><strong>Artistes :</strong> ${sp.artistes}</p>
      <p><strong>Style :</strong> ${sp.style}</p>
      <p><strong>Horaire :</strong> ${sp.horaire}</p>
      <p>${sp.description}</p>
      ${sp.video ? `<p><a href="${sp.video}" target="_blank" rel="noopener">Voir l'extrait vidéo</a></p>` : ''}
    </div>
  `;
}

export function renderSoireeDetailView(soiree: SoireeDetail): string {
  const spectaclesList = soiree.spectacles.map(renderSpectacle).join('');

  return `
    <section class="soiree-detail">
      <h2>${soiree.nom}</h2>
      <p class="thematique"><em>Thématique : ${soiree.thematique}</em></p>
      
      <div class="infos-pratiques">
        <p><strong>Date & Horaire :</strong> ${soiree.date} - ${soiree.horaire}</p>
        <p><strong>Lieu :</strong> ${soiree.lieu}</p>
        <p><strong>Tarifs :</strong> Normal ${soiree.tarifNormal} € | Réduit ${soiree.tarifReduit} €</p>
      </div>

      <!-- Emplacement prêt pour la fonctionnalité 6 (Formulaire d'achat de billets) -->
      <div id="cart-selection-container"></div>

      <h3>Programme de la soirée</h3>
      <div class="spectacles-grid">
        ${spectaclesList}
      </div>
    </section>
  `;
}