import { fetchJauges } from "./api/api";
import { renderJauges } from "./api/view";
import { initAuthMenu } from "./api/auth";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    initAuthMenu();

    try {
        errorp.textContent = "Chargement...";
                
        const jauges = await fetchJauges();

        const stats = jauges.reduce((acc, item) => {
            acc.totalBilletsVendus += item.jauge.placesVendues;
            acc.totalRecettes += item.jauge.recette;
            acc.totalCapacite += item.jauge.capacite;
            return acc;
        }, { totalBilletsVendus: 0, totalRecettes: 0, totalCapacite: 0 });

        const total = {
            billetsVendus: stats.totalBilletsVendus,
            recettes: stats.totalRecettes,
            capacite: stats.totalCapacite,
            placesRestantes: stats.totalCapacite - stats.totalBilletsVendus,
            tauxRemplissage: stats.totalCapacite > 0 ? Number(((stats.totalBilletsVendus / stats.totalCapacite) * 100).toFixed(2)) : 0
        };

        console.log(total);

        container.innerHTML = renderJauges(jauges, total);
    } catch (error) {
        errorp.textContent = error.message;
    }
}

void main();