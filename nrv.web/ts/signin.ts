import { API_BASE_URL } from "./api/config";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector<HTMLFormElement>("#login-form");

    if (!form) return;

    form.addEventListener("submit", async (e: Event) => {
        e.preventDefault();

        const formData = new FormData(form);
        const prenom = formData.get("prenom") as string;
        const nom = formData.get("nom") as string;
        const email = formData.get("email") as string;
        const mdp = formData.get("mdp") as string;
        const mdp2 = formData.get("mdp2") as string;

        if (!prenom || !nom || !email || !mdp || !mdp2) {
            alert("Veuillez remplir tous les champs.");
            return;
        }

        if (mdp !== mdp2) {
            alert("Les mots de passe ne correspondent pas.");
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}/signin`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ prenom, nom, email, mdp })
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || "Erreur lors de la création du compte.");
                return;
            }

            // Inscription réussie -> Stockage automatique du JWT (connexion directe)
            localStorage.setItem("auth_token", data.token);

            // Redirection
            window.location.href = "./index.html";
        } catch (error) {
            console.error("Erreur d'inscription :", error);
            alert("Impossible de contacter le serveur.");
        }
    });
});