import Handlebars from "handlebars";

import { ... } from "./types";

export function renderArticles(articles: ArticleList["articles"]): void {
    const zone = document.getElementById("articles");
    if (!zone) return;

    const source = (document.getElementById("tpl-articles") as HTMLScriptElement).innerHTML;
    const template = Handlebars.compile(source);
    zone.innerHTML = template({ articles });
}
