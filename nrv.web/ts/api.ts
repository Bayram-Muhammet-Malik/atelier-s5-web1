import { API_BASE_URL, API_PATH } from "./config";
import { ... } from "./types";

export async function getArticles(path: string): Promise<ArticleList> {
    console.log(`${API_BASE_URL}${API_PATH}${path}`);
    const response = await fetch(`${API_BASE_URL}${API_PATH}${path}`);

    if (!response.ok) {
        throw new Error("Erreur lors du chargement des articles");
    }

    return response.json() as Promise<ArticleList>;
}

export async function getCategories(): Promise<CategoryList> {
    const response = await fetch(`${API_BASE_URL}${API_PATH}categories`);

    if (!response.ok) {
        throw new Error("Erreur lors du chargement des catégories");
    }

    return response.json() as Promise<CategoryList>;
}
