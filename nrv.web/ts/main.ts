import { fetchSpectacles, fetchStyles, fetchMinMaxDates } from "./api/api";
import { renderSpectacles } from "./api/view";

const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
    if (!container) throw new Error("Élément #app introuvable.");
    if (!errorp) throw new Error("Élément #error introuvable.");

    const params = new URLSearchParams(window.location.search);
    const style = params.get("style");
    const date = params.get("date");

    try {
        errorp.innerHTML = "<p>Chargement...</p>";
        
        const spectacles = await fetchSpectacles(style, date);
        const styles = await fetchStyles();
        const minmax = await fetchMinMaxDates();
        
        container.innerHTML = renderSpectacles(spectacles, styles, minmax);
        errorp.innerHTML = "";

        const styleSelect = container.querySelector<HTMLSelectElement>("#style-select");
        const dateInput = container.querySelector<HTMLInputElement>("#date-input");

        if (styleSelect && style) styleSelect.value = style;
        if (dateInput && date) dateInput.value = date;

        const handleFilterChange = () => {
            const currentStyle = styleSelect?.value || "";
            const currentDate = dateInput?.value || "";

            const newParams = new URLSearchParams();
            if (currentStyle) newParams.set("style", currentStyle);
            if (currentDate) newParams.set("date", currentDate);

            window.location.search = newParams.toString();
        };

        styleSelect?.addEventListener("change", handleFilterChange);
        dateInput?.addEventListener("change", handleFilterChange);

    } catch (error) {
        console.error(error);
        errorp.innerHTML = "<p>Impossible de charger les spéctacles.</p>";
    }
}

void main();