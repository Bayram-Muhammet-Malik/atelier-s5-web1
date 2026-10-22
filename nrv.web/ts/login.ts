import { login } from "./api/auth";
import { initAuthMenu } from "./api/auth";

document.addEventListener("DOMContentLoaded", () => {
    initAuthMenu();

    const form = document.querySelector<HTMLFormElement>("#login-form");
    if (!form) return;

    form.addEventListener("submit", async (e: SubmitEvent) => {
        e.preventDefault();

        const formData = new FormData(form);

        const email = formData.get("email") as string;
        const mdp = formData.get("mdp") as string;

        if (!email || !mdp) {
            alert("Veuillez remplir tous les champs.");
            return;
        }

        try {
            const data = await login({email, mdp});

            localStorage.setItem("auth_token", data.token);

            window.location.href = "./index.html";
        } catch (error) {
            console.error("Erreur de connexion :", error);

            alert(error instanceof Error ? error.message : "Impossible de contacter le serveur.");
        }
    });
});