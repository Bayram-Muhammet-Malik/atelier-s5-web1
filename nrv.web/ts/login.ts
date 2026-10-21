import { API_BASE_URL } from "./api/config";

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector<HTMLFormElement>("#login-form");

    if (!form) return;

    form.addEventListener("submit", async (e: Event) => {
        e.preventDefault();

        const formData = new FormData(form);
        const email = formData.get("email") as string;
        const mdp = formData.get("mdp") as string;

        if (!email || !mdp) {
            alert("Veuillez remplir tous les champs.");
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}/login`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ email, mdp })
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || "Identifiants incorrects.");
                return;
            }

            // Enregistrement du JWT dans le localStorage
            localStorage.setItem("auth_token", data.token);

            // Redirection vers l'accueil ou la page précédente
            window.location.href = "./index.html";
        } catch (error) {
            console.error("Erreur de connexion :", error);
            alert("Impossible de contacter le serveur.");
        }
    });
});