import { signup } from "./api/auth";
import { initAuthMenu } from "./api/auth";

document.addEventListener("DOMContentLoaded", () => {
    initAuthMenu();

    const form = document.querySelector<HTMLFormElement>("#signup-form");

    if (!form) return;

    form.addEventListener("submit", async (e: SubmitEvent) => {
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
            const data = await signup({prenom, nom, email, mdp});

            localStorage.setItem("auth_token", data.token);

            window.location.href = "./index.html";
        } catch (error) {
            alert(error instanceof Error ? error.message : "Impossible de contacter le serveur.");
        }
    });
});