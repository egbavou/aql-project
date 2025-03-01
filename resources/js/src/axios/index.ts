import axios, { AxiosInstance, InternalAxiosRequestConfig, AxiosResponse, AxiosError } from "axios";
import { useAuthStore } from "@/store/auth";

const axiosUser: AxiosInstance = axios.create({
    baseURL: "http://127.0.0.1:8000",
});

axiosUser.interceptors.request.use((config: InternalAxiosRequestConfig) => {
    const authStore = useAuthStore()
    const token = localStorage.getItem("token");

    config.headers.Accept = "application/json";
    config.headers["Content-Type"] = "application/json";
    if (token) {
        config.headers.Authorization = `Bearer ${authStore.user?.token}`;
    }

    return config;
});

/* axiosUser.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error: AxiosError) => {
        if (error.response?.status === 401) {
            localStorage.clear();
            window.location.href = "/";
        }
        return Promise.reject(error);
    }
); */

export default axiosUser;
