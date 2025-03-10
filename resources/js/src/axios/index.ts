import axios, { AxiosError, AxiosInstance, AxiosResponse, InternalAxiosRequestConfig } from "axios";
import { useAuthStore } from "@/store/auth";

const axiosUser: AxiosInstance = axios.create({
    baseURL: `${import.meta.env.VITE_API_URL}`,
});

axiosUser.interceptors.request.use((config: InternalAxiosRequestConfig) => {
    const authStore = useAuthStore()

    config.headers.Accept = "application/json";
    config.headers["Content-Type"] = "application/json";

    config.headers.Authorization = `Bearer ${authStore.user?.token}`;

    return config;
});

axiosUser.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error: AxiosError) => {
        const authStore = useAuthStore()
        if (error.response?.status == 401) {
            localStorage.removeItem("user");
            authStore.user = null
            if (localStorage.getItem("one_redirect") != "true") {
                window.location.href = "/";
            }
            localStorage.setItem("one_redirect", "true")
        }
        return Promise.reject(error);
    }
);

export default axiosUser;
