import axios from "axios";
import { API_URL } from "@/src/lib/api";

const api = axios.create({
  baseURL: API_URL,
});

export default api;
