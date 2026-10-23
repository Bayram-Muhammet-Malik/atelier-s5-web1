import Handlebars from "handlebars";
import { Spectacle, Style, Jauge } from './types';

export function renderSpectacles(spectacles: Spectacle[], styles: Style[], minmax: { min: string; max: string }, page?: number, previousPageUrl?: string | null, nextPageUrl?: string | null): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#spectacles-template");

    if (!templateElement) throw new Error("Le template #spectacles-template est introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);

    return template({ spectacles, styles, minmax, page, previousPageUrl, nextPageUrl });
}

export function renderJauges(jauges: Jauge[], total: { billetsVendus: number; recettes: number; capacite: number; placesRestantes: number; tauxRemplissage: number; }): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#admin-template");

    if (!templateElement) throw new Error("Template #admin-template introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);
    return template({jauges, total});
}

export function renderPass(soirees: Soiree[]): string {
    const templateElement = document.querySelector<HTMLScriptElement>("#pass-template");

    if (!templateElement) throw new Error("Template #pass-template introuvable.");

    const template = Handlebars.compile(templateElement.innerHTML);
    return template({ soirees });
}

export function renderTemplate(templateId: string, data: object): string {
    const element = document.querySelector<HTMLScriptElement>(`${templateId}`);

    if (!element) throw new Error(`Template ${templateId} introuvable.`);

    return Handlebars.compile(element.innerHTML)(data);
}

