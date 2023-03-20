import axios from "axios";

axios.defaults.withCredentials = true;
// console.log(import.meta.env);
const api = axios.create({
    baseURL: "/api",
    headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Expose-Headers": "*",
        "X-Requested-With": "XMLHttpRequest",
    },
});

const base = axios.create({
    // baseURL: import.meta.env.VITE_APP_URL,
    headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Expose-Headers": "*",
        "X-Requested-With": "XMLHttpRequest",
    },
});

export { api, base };
