import { fetchSoirees } from "./api/api";
import { renderPass } from "./api/view";
import { initAuthMenu } from "./api/auth";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    initAuthMenu();

    try {
        errorp.textContent = "Chargement...";
        
        const soirees = await fetchSoirees();
        
        container.innerHTML = renderPass(soirees);

        const form = document.querySelector<HTMLFormElement>("#pass-form");
        if (!form) return;

        const checkboxes = Array.from(form.querySelectorAll<HTMLInputElement>('.soirees-list input[type="checkbox"]'));
        const counter = form.querySelector<HTMLElement>(".soirees-list > div:first-child .badge");

        if (!counter) return;

        const maxSelections = 3;

        function updateCounter(): void {
            const selectedCount = checkboxes.filter((checkbox) => checkbox.checked).length;

            counter.textContent = `${selectedCount} / ${maxSelections} sélectionnées`;

            checkboxes.forEach((checkbox) => {
                if (!checkbox.checked) {
                    checkbox.disabled = selectedCount >= maxSelections;
                }
            });
        }

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", updateCounter);
        });

        updateCounter();
    } catch (error) {
        errorp.textContent = error.message;
    }
}

void main();