const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    const params = new URLSearchParams(window.location.search);
    const spectacleId = params.get("spectacleId");

    try {
        errorp.innerHTML = "<p>Chargement...</p>";
    } catch (error) {
        console.error(error);
        errorp.innerHTML = "<p>Impossible de charger la soirée.</p>";
    }
}

void main();