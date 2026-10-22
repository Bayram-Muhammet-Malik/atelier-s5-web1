import { fetchSpectacles, fetchStyles, fetchMinMaxDates, } from "./api/api";

import { renderSpectacles } from "./api/view";

const PAGE_SIZE = 6;
const container = document.querySelector<HTMLElement>("#app");
const errorp = document.querySelector<HTMLElement>("#error");

async function main(): Promise<void> {
  if (!container) throw new Error("Élément #app introuvable.");
  if (!errorp) throw new Error("Élément #error introuvable.");

  const params = new URLSearchParams(window.location.search);

  const style = params.get("style");
  const date = params.get("date");

  const parsedPage = Number.parseInt(params.get("page") ?? "1", 10);
  const page = parsedPage >= 1 ? parsedPage : 1;
  const offset = (page - 1) * PAGE_SIZE;

  try {
    errorp.innerHTML = "<p>Chargement...</p>";

    const spectacles = await fetchSpectacles(style, date, PAGE_SIZE, offset,);
    const styles = await fetchStyles();
    const minmax = await fetchMinMaxDates();

    const createPageUrl = (pageNumber: number): string => {
      const pageParams = new URLSearchParams();

      if (style) pageParams.set("style", style);
      if (date) pageParams.set("date", date);

      pageParams.set("page", String(pageNumber));

      return `?${pageParams.toString()}`;
    };

    const previousPageUrl = spectacles.hasPreviousPage ? createPageUrl(page - 1) : null;
    const nextPageUrl = spectacles.hasNextPage ? createPageUrl(page + 1) : null;

    container.innerHTML = renderSpectacles(spectacles.spectacles, styles, minmax, page, previousPageUrl, nextPageUrl,);

    const styleSelect = container.querySelector<HTMLSelectElement>("#style-select",);
    const dateInput = container.querySelector<HTMLInputElement>("#date-input",);

    if (styleSelect && style) styleSelect.value = style;
    if (dateInput && date) dateInput.value = date;

    const handleFilterChange = (): void => {
      const currentStyle = styleSelect?.value || "";
      const currentDate = dateInput?.value || "";
      const newParams = new URLSearchParams();

      if (currentStyle) newParams.set("style", currentStyle);
      if (currentDate) newParams.set("date", currentDate);

      newParams.set("page", "1");
      window.location.search = newParams.toString();
    };

    styleSelect?.addEventListener("change", handleFilterChange);
    dateInput?.addEventListener("change", handleFilterChange);
  } catch (error) {
    console.error(error);
    errorp.innerHTML = "<p>Impossible de charger les spectacles.</p>";
  }
}

void main();