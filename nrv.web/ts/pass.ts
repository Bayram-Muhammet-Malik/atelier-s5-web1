import { fetchSoirees } from "./api/api";
import { renderPass } from "./api/view";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    try {
        errorp.innerHTML = "<p>Chargement...</p>";
        
        const soirees = await fetchSoirees();
        
        container.innerHTML = renderPass(soirees);
        errorp.innerHTML = "";

    } catch (error) {
        console.error(error);
        errorp.innerHTML = "<p>Impossible de charger les spéctacles.</p>";
    }
}

void main();