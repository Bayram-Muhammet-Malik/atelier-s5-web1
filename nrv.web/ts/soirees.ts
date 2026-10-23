import { fetchSoireeDetail, fetchSoireeBySpectacleId } from "./api/api";
import { renderTemplate } from "./api/view";
import { initAuthMenu } from "./api/auth";
import { addToCart, type CartItem, type TarifType } from "./api/cart";

function setupFormHandler(soiree: any): void {
    const form =
        document.querySelector<HTMLFormElement>("#ticket-form");

    const messageEl =
        document.querySelector<HTMLElement>("#cart-message");

    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const tarifType = formData.get("tarifType") as TarifType;
        const quantite = Number(formData.get("quantite"));

        if (!["normal", "reduit"].includes(tarifType)) {
            if (messageEl) {
                messageEl.textContent = "Veuillez choisir un tarif valide.";
                messageEl.className = "error-message";
            }
            return;
        }

        const prixUnitaire =
            tarifType === "normal"
                ? soiree.tarifNormal
                : soiree.tarifReduit;

        try {
            const item: CartItem = {
                soireeId: soiree.id,
                nomSoiree: soiree.nom,
                tarifType,
                prixUnitaire,
                quantite
            };

            addToCart(item);

            if (messageEl) {
                messageEl.textContent =
                    `${quantite} billet(s) ajouté(s) au panier.`;

                messageEl.className = "success-message";
            }
        } catch (error) {
            if (messageEl) {
                messageEl.textContent =
                    error instanceof Error
                        ? error.message
                        : "Impossible d'ajouter au panier.";

                messageEl.className = "error-message";
            }
        }
    });
}

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    initAuthMenu();

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id");
    const spectacleId = params.get("spectacleId");

    if (!id && !spectacleId) {
        errorp.textContent = "Identifiant de soirée ou de spectacle manquant.";
        return;
    }

    try {
        errorp.textContent = "Chargement...";

        let soiree;
        if (id) {
            soiree = await fetchSoireeDetail(id);
        } else if (spectacleId) {
            soiree = await fetchSoireeBySpectacleId(spectacleId);
        }

        if (!soiree) {
            throw new Error("Soirée introuvable");
        }

        container.innerHTML = renderTemplate("#soiree-detail-template", soiree);
        setupFormHandler(soiree);
    } catch (error) {
        errorp.textContent = error.message;
    }
}

void main();