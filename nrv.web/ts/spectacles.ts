import { fetchSpectacles } from "./api/api";
import { renderSpectacles } from "./api/view";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    const params = new URLSearchParams(window.location.search);
    //const filter = params.get("filter");

    try {
        errorp.innerHTML = "<p>Chargement...</p>";
        const spectacles = await fetchSpectacles();
        container.innerHTML = renderSpectacles(spectacles);
    } catch (error) {
        console.error(error);
        errorp.innerHTML = "<p>Impossible de charger les spéctacles.</p>";
    }
}

void main();