import Handlebars from "handlebars";
import { SoireeDetail, Spectacle, Style } from './types';

export function renderSpectacles(spectacles: Spectacle[], styles: Style[], minmax: { min: string; max: string }, page: number, previousPageUrl: string | null, nextPageUrl: string | null): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#spectacles-template");

    if (!templateElement) throw new Error("Le template #spectacles-template est introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);

    return template({spectacles, styles, minmax, page, previousPageUrl, nextPageUrl});
}

export function renderSoireeDetailView(soiree: SoireeDetail): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#soiree-detail-template");

    if (!templateElement) throw new Error("Template #soiree-detail-template introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);
    return template(soiree);
}