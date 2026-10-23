import { fetchBillet } from "./api/api";
import { renderBillet } from "./api/view";
import { initAuthMenu } from "./api/auth";
import { BilletSoiree } from "./api/types";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    initAuthMenu();

    const params = new URLSearchParams(window.location.search);
    const token = params.get("token");

    if (!token) {
        errorp.textContent = "Aucun billet sélectionné : ouvrez votre billet depuis le lien reçu après le paiement.";
        return;
    }

    try {
        errorp.textContent = "Chargement...";

        const billet = await fetchBillet(token);

        const soirees: BilletSoiree[] = billet.soirees && billet.soirees.length > 0
            ? billet.soirees
            : [{ idSoiree: billet.idSoiree, nomSoiree: billet.nomSoiree, lieu: billet.lieu, dateSoiree: billet.dateSoiree, heureDebut: billet.heureDebut }];

        const soireesAffichees = soirees.map((soiree) => ({
            ...soiree,
            date: soiree.dateSoiree.split("-").reverse().join("/"),
            heure: soiree.heureDebut.slice(0, 5).replace(":", "h"),
        }));
        const prix = billet.prixPaye.toFixed(2).replace(".", ",");

        container.innerHTML = renderBillet(billet, soireesAffichees, prix);

        const printButton = document.querySelector<HTMLButtonElement>("#print-btn");
        printButton?.addEventListener("click", () => window.print());
    } catch (error) {
        errorp.textContent = error instanceof Error ? error.message : "Impossible de charger le billet.";
    }
}

void main();
