export type TarifType = "normal" | "reduit";

export interface CartItem {
    soireeId: string | number;
    nomSoiree: string;
    tarifType: TarifType;
    prixUnitaire: number;
    quantite: number;
}

const CART_KEY = "cart";
const QUANTITE_MAX = 10;

export function getCart(): CartItem[] {
    try {
        const data: unknown = JSON.parse(
            localStorage.getItem(CART_KEY) ?? "[]"
        );

        if (!Array.isArray(data)) return [];

        return data.filter((item): item is CartItem => {
            return (
                item !== null &&
                typeof item === "object" &&
                (typeof item.soireeId === "string" ||
                    typeof item.soireeId === "number") &&
                typeof item.nomSoiree === "string" &&
                (item.tarifType === "normal" ||
                    item.tarifType === "reduit") &&
                typeof item.prixUnitaire === "number" &&
                Number.isFinite(item.prixUnitaire) &&
                item.prixUnitaire >= 0 &&
                Number.isInteger(item.quantite) &&
                item.quantite >= 1 &&
                item.quantite <= QUANTITE_MAX
            );
        });
    } catch {
        return [];
    }
}

function saveCart(cart: CartItem[]): void {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

export function addToCart(newItem: CartItem): void {
    if (
        !Number.isInteger(newItem.quantite) ||
        newItem.quantite < 1 ||
        newItem.quantite > QUANTITE_MAX
    ) {
        throw new Error("La quantité doit être comprise entre 1 et 10.");
    }

    if (
        !Number.isFinite(newItem.prixUnitaire) ||
        newItem.prixUnitaire < 0
    ) {
        throw new Error("Le prix du billet est invalide.");
    }

    const cart = getCart();

    const index = cart.findIndex(
        item =>
            String(item.soireeId) === String(newItem.soireeId) &&
            item.tarifType === newItem.tarifType
    );

    if (index !== -1) {
        const nouvelleQuantite =
            cart[index].quantite + newItem.quantite;

        if (nouvelleQuantite > QUANTITE_MAX) {
            throw new Error(
                "Maximum 10 billets par soirée et par tarif."
            );
        }

        cart[index].quantite = nouvelleQuantite;
    } else {
        cart.push(newItem);
    }

    saveCart(cart);
}

export function removeFromCart(
    soireeId: string | number,
    tarifType: TarifType
): void {
    const cart = getCart().filter(
        item =>
            !(
                String(item.soireeId) === String(soireeId) &&
                item.tarifType === tarifType
            )
    );

    saveCart(cart);
}

export function updateCartQuantity(
    soireeId: string | number,
    tarifType: TarifType,
    quantite: number
): void {
    if (
        !Number.isInteger(quantite) ||
        quantite < 1 ||
        quantite > QUANTITE_MAX
    ) {
        throw new Error("La quantité doit être comprise entre 1 et 10.");
    }

    const cart = getCart();
    const item = cart.find(
        ligne =>
            String(ligne.soireeId) === String(soireeId) &&
            ligne.tarifType === tarifType
    );

    if (!item) {
        throw new Error("Cet article n'est plus dans le panier.");
    }

    item.quantite = quantite;
    saveCart(cart);
}

export function getCartTotal(): number {
    return getCart().reduce(
        (total, item) => total + item.prixUnitaire * item.quantite,
        0
    );
}

export function clearCart(): void {
    localStorage.removeItem(CART_KEY);
}