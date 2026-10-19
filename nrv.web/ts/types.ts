export interface ArticleList {
    articles: {
        article: {
            titre: string;
            date_creation: string;
            auteur_id: number;
        };
        links: {
            self: { href: string };
        };
    }[];
}

export interface Article {
    article: {
        id: number;
        titre: string;
        resume: string;
        contenu: string;
        date_creation: string;
        date_publication: string;
        auteur_id: number;
        categorie_id: number;
    };
}