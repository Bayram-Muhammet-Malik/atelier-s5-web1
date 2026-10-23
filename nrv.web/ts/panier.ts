import {
      getCart,
      getCartTotal,
      removeFromCart,
      updateCartQuantity,
      clearCart
} from "./api/cart";

import {
      creerCommande,
      payerCommande,
      fetchBillet,
      type BilletDTO
} from "./api/api";

import { initAuthMenu, isAuthenticated } from "./api/auth";

const app = document.querySelector<HTMLElement>("#app");
const message = document.querySelector<HTMLElement>("#message");
const paymentSection =
      document.querySelector<HTMLElement>("#payment-section");
const paymentForm =
      document.querySelector<HTMLFormElement>("#payment-form");
const ticketsSection =
      document.querySelector<HTMLElement>("#tickets-section");

let commandeIdEnCours: string | null = null;

function afficherMessage(texte: string, erreur = false): void {
      if (!message) return;

      message.textContent = texte;
      message.className = erreur ? "error-message" : "success-message";
}

function renderCart(): void {
      if (!app) return;

      const lignes = getCart().map(item => ({
            ...item,
            sousTotal: (item.prixUnitaire * item.quantite).toFixed(2),
            prixAffiche: item.prixUnitaire.toFixed(2),
            tarifLibelle: item.tarifType === "normal" ? "Normal" : "Réduit"
      }));

      app.innerHTML = renderTemplate("cart-template", {
            lignes,
            total: getCartTotal().toFixed(2),
            panierVide: lignes.length === 0,
            authentifie: isAuthenticated()
      });

      // Suppression d'une ligne.
      app.querySelectorAll<HTMLButtonElement>("[data-remove]")
            .forEach(button => {
                  button.addEventListener("click", () => {
                        const soireeId = button.dataset.soireeId;
                        const tarifType = button.dataset.tarifType;

                        if (
                              soireeId === undefined ||
                              (tarifType !== "normal" && tarifType !== "reduit")
                        ) {
                              return;
                        }

                        removeFromCart(soireeId, tarifType);
                        afficherMessage("Article retiré du panier.");
                        renderCart();
                  });
            });

      // Modification de la quantité.
      app.querySelectorAll<HTMLInputElement>("[data-quantity]")
            .forEach(input => {
                  input.addEventListener("change", () => {
                        const soireeId = input.dataset.soireeId;
                        const tarifType = input.dataset.tarifType;
                        const quantite = Number(input.value);

                        if (
                              soireeId === undefined ||
                              (tarifType !== "normal" && tarifType !== "reduit")
                        ) {
                              renderCart();
                              return;
                        }

                        try {
                              updateCartQuantity(
                                    soireeId,
                                    tarifType,
                                    quantite
                              );

                              afficherMessage("Quantité mise à jour.");
                        } catch (error) {
                              afficherMessage(
                                    error instanceof Error
                                          ? error.message
                                          : "Quantité invalide.",
                                    true
                              );
                        }

                        renderCart();
                  });
            });

      // Validation du panier : création d'une commande en attente.
      const checkoutForm =
            app.querySelector<HTMLFormElement>("#checkout-form");

      checkoutForm?.addEventListener("submit", async event => {
            event.preventDefault();

            if (!isAuthenticated()) {
                  afficherMessage(
                        "Connecte-toi avant de valider ta commande.",
                        true
                  );
                  return;
            }

            if (getCart().length === 0) {
                  afficherMessage("Ton panier est vide.", true);
                  return;
            }

            const button =
                  checkoutForm.querySelector<HTMLButtonElement>(
                        'button[type="submit"]'
                  );

            if (button) button.disabled = true;

            try {
                  const resultat = await creerCommande(getCart());

                  commandeIdEnCours = resultat.commande.id;

                  if (paymentSection) {
                        paymentSection.hidden = false;
                  }

                  if (ticketsSection) {
                        ticketsSection.innerHTML = "";
                  }

                  afficherMessage(
                        `Commande ${commandeIdEnCours} créée. Tu peux maintenant procéder au paiement.`
                  );

                  paymentSection?.scrollIntoView({ behavior: "smooth" });
            } catch (error) {
                  afficherMessage(
                        error instanceof Error
                              ? error.message
                              : "Impossible de créer la commande.",
                        true
                  );

                  if (button) button.disabled = false;
            }
      });
}

function extraireToken(href: string): string {
      const url = new URL(href, window.location.origin);
      const segments = url.pathname.split("/").filter(Boolean);
      const token = segments[segments.length - 1];

      if (!token) {
            throw new Error("Lien de billet invalide.");
      }

      return decodeURIComponent(token);
}

function afficherBillets(billets: BilletDTO[]): void {
      if (!ticketsSection) return;

      ticketsSection.innerHTML = renderTemplate("tickets-template", {
            billets: billets.map(billet => ({
                  ...billet,
                  prixAffiche: billet.prixPaye.toFixed(2),
                  typeBillet: billet.estPass ? "Pass" : "Billet"
            }))
      });

      ticketsSection.scrollIntoView({ behavior: "smooth" });
}

paymentForm?.addEventListener("submit", async event => {
      event.preventDefault();

      if (!isAuthenticated()) {
            afficherMessage("Tu dois être connecté pour payer.", true);
            return;
      }

      if (!commandeIdEnCours) {
            afficherMessage(
                  "Valide d'abord ton panier pour créer une commande.",
                  true
            );
            return;
      }

      const button =
            paymentForm.querySelector<HTMLButtonElement>(
                  'button[type="submit"]'
            );

      if (button) button.disabled = true;

      const formData = new FormData(paymentForm);

      try {
            const resultat = await payerCommande(commandeIdEnCours, {
                  numero: String(formData.get("numero") ?? "").replace(/\s/g, ""),
                  code: String(formData.get("code") ?? ""),
                  expiration: String(formData.get("expiration") ?? "")
            });

            // Le paiement est confirmé : le panier peut être vidé.
            clearCart();
            renderCart();

            if (paymentSection) {
                  paymentSection.hidden = true;
            }

            afficherMessage(
                  "Paiement confirmé ! Tes billets sont disponibles ci-dessous."
            );

            const liens = resultat.links?.billets ?? [];

            if (liens.length === 0) {
                  afficherMessage(
                        "Le paiement a réussi, mais aucun lien de billet n'a été retourné.",
                        true
                  );
                  return;
            }

            // Les billets ne sont consultés qu'après confirmation du paiement.
            const billets = await Promise.all(
                  liens.map(lien => fetchBillet(extraireToken(lien.href)))
            );

            afficherBillets(billets);

            commandeIdEnCours = null;
      } catch (error) {
            afficherMessage(
                  error instanceof Error
                        ? error.message
                        : "Impossible de finaliser le paiement.",
                  true
            );
      } finally {
            if (button) button.disabled = false;
      }
});

const printButton =
      document.querySelector<HTMLButtonElement>("#print-tickets");

printButton?.addEventListener("click", () => {
      window.print();
});

initAuthMenu();

if (paymentSection) {
      paymentSection.hidden = true;
}

renderCart();