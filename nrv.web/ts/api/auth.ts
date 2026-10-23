import { API_BASE_URL, API_PATH } from "./config";
import { AuthResponse } from './types';

const AUTH_TOKEN_KEY = "auth_token";

export function isAuthenticated(): boolean {
    return Boolean(localStorage.getItem(AUTH_TOKEN_KEY));
}

export function getAuthHeaders(): HeadersInit {
  const token = localStorage.getItem(AUTH_TOKEN_KEY);
  const headers: Record<string, string> = {
    "Content-Type": "application/json"
  };

  if (token) {
    headers["Authorization"] = `Bearer ${token}`;
  }

  return headers;
}

export async function login(email: string, mdp: string): Promise<AuthResponse> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({email, mdp})
  });

  const data = await response.json();

  if (!response.ok) throw new Error(data.error || "Erreur lors de la connexion.");

  return data;
}

export async function signup(prenom: string, nom: string, email: string, mdp: string): Promise<AuthResponse> {
  const response = await fetch(`${API_BASE_URL}${API_PATH}signup`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({prenom, nom, email, mdp})
  });

  const data = await response.json();

  if (!response.ok) throw new Error(data.error || "Erreur lors de la création du compte.");

  return data;
}

export function logout(): void {
    localStorage.removeItem(AUTH_TOKEN_KEY);

    window.location.href = "login.html";
}

export function initAuthMenu(): void {
  localStorage.getItem(AUTH_TOKEN_KEY);
    const menuContent = document.querySelector<HTMLElement>(".menu-content");
    const loginButton = document.querySelector<HTMLAnchorElement>("#login-btn");

    if (!menuContent || !isAuthenticated()) return;

    loginButton?.remove();

    const authMenu = document.createElement("template");
    authMenu.innerHTML = `
        <a href="billets.html"><h1>Mes billets</h1></a>
        <a href="#" id="logout-btn"><h1>Déconnexion</h1></a>
    `;

    const logoutButton = authMenu.content.querySelector<HTMLAnchorElement>("#logout-btn");

    menuContent.appendChild(authMenu.content);

    logoutButton?.addEventListener("click", (event) => {
        event.preventDefault();
        logout();
    });
}