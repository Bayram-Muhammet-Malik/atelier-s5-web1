import Handlebars from "handlebars";
import { SoireeDetail, Spectacle } from './types';

export function renderSpectacles(spectacles: Spectacle[]): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#spectacles-template");

    if (!templateElement) throw new Error("Le template #spectacles-template est introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);

    return template({spectacles});
}

export function renderSoireeDetailView(soiree: SoireeDetail): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#soiree-detail-template");

    if (!templateElement) throw new Error("Template #soiree-detail-template introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);
    return template(soiree);
}