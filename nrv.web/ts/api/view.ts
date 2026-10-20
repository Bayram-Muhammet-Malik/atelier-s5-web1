import Handlebars from "handlebars";
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
    const templateElement = document.querySelector<HTMLScriptElement>("#soiree-detail-template");

    if (!templateElement) throw new Error("Template #soiree-detail-template introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);
    return template(soiree);
}