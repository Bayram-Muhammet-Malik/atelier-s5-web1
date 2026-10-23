import { fetchBillet, fetchMyBillets } from "./api/api";
import { renderTemplate, renderMyBillets } from "./api/view";
import { initAuthMenu } from "./api/auth";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    initAuthMenu();

    const params = new URLSearchParams(window.location.search);
    const token = params.get("token");

    errorp.textContent = "Chargement...";

    try {
        if (!token) {
            const billets = await fetchMyBillets();
            console.log(billets);

            container.innerHTML = renderMyBillets(billets);
            return;
        }

        const billet = await fetchBillet(token);
        console.log(billet);

        container.innerHTML = renderTemplate("#billet-template", billet);

        const printButton = document.querySelector<HTMLButtonElement>("#print-btn");
        printButton?.addEventListener("click", () => window.print());
    } catch (error) {
        errorp.textContent = error.message;
    }
}

void main();
