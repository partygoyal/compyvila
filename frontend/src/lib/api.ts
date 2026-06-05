export const API_URL =
    process.env.NEXT_PUBLIC_API_URL ||
    "https://api.compyvila.com/api";

export const STORAGE_URL =
    process.env.NEXT_PUBLIC_STORAGE_URL ||
    "https://api.compyvila.com/storage";

export function authHeaders() {
    const token =
        localStorage.getItem("token") ||
        sessionStorage.getItem("token");

    return {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
    };
}
