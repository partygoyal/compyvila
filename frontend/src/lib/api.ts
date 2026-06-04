const API_URL =
    process.env.NEXT_PUBLIC_API_URL ||
    "http://127.0.0.1:8000/api";

const STORAGE_URL =
    API_URL.replace(/\/api\/?$/, "/storage");

export function authHeaders() {

    const token =
        localStorage.getItem("token") ||
        sessionStorage.getItem("token");

    return {
        Authorization: `Bearer ${token}`,
        Accept: "application/json",
    };
}

export { API_URL, STORAGE_URL };
