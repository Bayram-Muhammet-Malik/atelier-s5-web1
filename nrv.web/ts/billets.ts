import { fetchBillet } from "./api/api";
import { renderBillet } from "./api/view";
import { initAuthMenu } from "./api/auth";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    initAuthMenu();

    const params = new URLSearchParams(window.location.search);
    const token = params.get("token");

    if (!token) {
        errorp.textContent = "Aucun billet sélectionné.";
        return;
    }

    try {
        errorp.textContent = "Chargement...";

        const billet = await fetchBillet(token);

        container.innerHTML = renderBillet(billet);

        const printButton = document.querySelector<HTMLButtonElement>("#print-btn");
        printButton?.addEventListener("click", () => window.print());
    } catch (error) {
        errorp.textContent = error.message;
    }
}

void main();
