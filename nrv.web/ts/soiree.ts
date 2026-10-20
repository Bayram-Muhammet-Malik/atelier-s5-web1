import { fetchSoireeDetail, fetchSoireeBySpectacleId } from "./api/api";
import { renderSoireeDetailView } from "./api/view";

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

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");

    const params = new URLSearchParams(window.location.search);
    const id = params.get("id") || params.get("spectacleId");

    if (!id) {
        container.innerHTML = "<section class='content-main'><p id='error'>Identifiant de soirée manquant dans l'URL.</p></section>";
        return;
    }

    try {
        // Appelle fetchSoireeDetail avec l'UUID de la soirée
        const soiree = await fetchSoireeDetail(id);

        container.innerHTML = renderSoireeDetailView(soiree);
        setupFormHandler(soiree);
    } catch (error) {
        console.error("Erreur lors de la récupération de la soirée :", error);
        container.innerHTML = "<section class='content-main'><p id='error'>Impossible de charger la soirée. Vérifiez la connexion à l'API.</p></section>";
    }
}

void main();