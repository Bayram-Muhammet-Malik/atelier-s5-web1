import { fetchSoireeDetail, fetchSoireeBySpectacleId } from "./api/api";
import { renderSoireeDetailView } from "./api/view";

/* A fix marche pas totalement ou alors parce que la connection n'existe pas encore mais je laisse le code au cas où
interface CartItem {
    soireeId: string | number;
    nomSoiree: string;
    tarifType: 'normal' | 'reduit';
    prixUnitaire: number;
    quantite: number;
}

function addToCart(newItem: CartItem): void {
    const cart: CartItem[] = JSON.parse(localStorage.getItem('cart') || '[]');
    const existingIndex = cart.findIndex(
        item => item.soireeId === newItem.soireeId && item.tarifType === newItem.tarifType
    );

    if (existingIndex > -1) {
        cart[existingIndex].quantite += newItem.quantite;
    } else {
        cart.push(newItem);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
}

function setupFormHandler(soiree: any): void {
    const form = document.querySelector<HTMLFormElement>("#ticket-form");
    const messageEl = document.querySelector<HTMLElement>("#cart-message");

    if (!form) return;

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(form);
        const tarifType = formData.get("tarifType") as 'normal' | 'reduit';
        const quantite = parseInt(formData.get("quantite") as string, 10) || 1;
        const prixUnitaire = tarifType === 'normal' ? soiree.tarifNormal : soiree.tarifReduit;

        addToCart({
            soireeId: soiree.id,
            nomSoiree: soiree.nom,
            tarifType,
            prixUnitaire,
            quantite
        });

        if (messageEl) {
            messageEl.textContent = `✓ ${quantite} billet(s) (${tarifType}) ajouté(s) au panier !`;
            messageEl.className = "success-message";

            setTimeout(() => {
                messageEl.className = "hidden";
            }, 3000);
        }
    });
}

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");
*/

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    const params = new URLSearchParams(window.location.search);
    const spectacleId = params.get("spectacleId");

    if (!spectacleId) {
        errorp.innerHTML = "<p>Identifiant du spectacle manquant.</p>";
        return;
    }

    try {
        errorp.innerHTML = "<p>Chargement...</p>";
        const soiree = await fetchSoireeBySpectacleId(spectacleId);
        container.innerHTML = renderSoireeDetailView(soiree);
    } catch (error) {
        console.error(error);
        errorp.innerHTML = "<p>Impossible de charger la soirée.</p>";
    }
}

void main();