import { defineStore } from 'pinia'
import { ref } from 'vue'
import axios from 'axios';
import { useAuthStore } from './auth';
import { axiosError, downloadFile } from '@/helpers';

export interface DocumentData {
    title: string;
    author: string;
    pages: number;
    language: string;
    visibility: string;
    tags: string[];
    file: File | null;
}

interface Tag {
    id: number;
    name: string;
    created_at: string;
    updated_at: string;
}

interface Document {
    id: number;
    title: string;
    downloads: number;
    size: number;
    author: string;
    pages: number;
    language: string;
    visibility: string;
    token: string | null;
    user_id: number;
    created_at: string;
    tags: Tag[];
}

interface ShareDocumentData {
    email: string;
}

export const useDocumentStore = defineStore('documents', () => {
    const loading = ref(false), loading2 = ref(false)
    const errors = ref<any>(null);
    const authStore = useAuthStore();
    const documents = ref<Document[]>([]);
    const document = ref<Document>();
    const current_page = ref(1);
    const last_page = ref(1);
    const total = ref(0);

    async function getDocuments(page: number = current_page.value) {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });
            const per_page: number = 12;
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/documents`, {
                headers: headers,
                params: {
                    page: page, per_page: per_page
                }
            });

            loading.value = false

            documents.value = response.data.data
            current_page.value = response.data.current_page
            last_page.value = response.data.last_page
            total.value = response.data.total

            return response.data.data
            // console.log(response)
        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function getDocumentsMe(cat: string = "created") {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });
            const per_page: number = 12;
            const page: number = current_page.value
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/documents/${cat}`, {
                headers: headers,
                params: {
                    page: page, per_page: per_page
                }
            });

            loading.value = false

            documents.value = response.data.data
            current_page.value = response.data.current_page
            last_page.value = response.data.last_page
            total.value = response.data.total

            return response.data.data
            // console.log(response)
        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function addDocument(formdata: DocumentData) {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });

            headers["Content-Type"] = "multipart/form-data"

            const response = await axios.post(`${import.meta.env.VITE_API_URL}/api/documents`, formdata, {
                headers: headers
            });

            loading.value = false;

            if (response.status == 200) return [true, response.data.title]

        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function updateDocument(id: number, formdata: DocumentData) {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });

            headers["Content-Type"] = "multipart/form-data"

            const response = await axios.post(`${import.meta.env.VITE_API_URL}/api/documents/${id}`, formdata, {
                headers: headers
            });

            loading.value = false;

            if (response.status == 200) return [true, response.data.title]

        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function getDocumentById(id: number) {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/documents/${id}`, {
                headers: headers
            });

            loading.value = false

            document.value = response.data

            return response.data
        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function deleteDocument(id: number) {
        loading2.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });
            const response = await axios.delete(`${import.meta.env.VITE_API_URL}/api/documents/${id}`, {
                headers: headers
            });

            loading2.value = false

            if (response.status == 204) {
                return true
            }
        } catch (error) {
            loading2.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function downloadDocument(id: number): Promise<void> {
        loading2.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });
            const response = await axios.get(`${import.meta.env.VITE_API_URL}/api/documents/download/${id}`, {
                responseType: "blob",
                headers: headers
            });

            loading2.value = false

            return downloadFile(document.value?.title, response.data, "application/pdf");
        } catch (error) {
            loading2.value = false
            errors.value = axiosError(error)
        }
    }

    async function shareDocument(id: number, formdata: ShareDocumentData) {
        loading2.value = true
        errors.value = null
        try {
            let headers = {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "Authorization": `Bearer ${authStore.user?.token}`
            }
            await axios.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`, {
                headers: headers
            });
            const response = await axios.post(`${import.meta.env.VITE_API_URL}/api/documents/${id}/private-share`, formdata, {
                headers: headers
            });

            loading2.value = false

            return true

        } catch (error) {
            loading2.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    return {
        documents,
        document,
        current_page,
        last_page,
        total,
        loading,
        loading2,
        errors,
        getDocumentById,
        addDocument,
        getDocuments,
        getDocumentsMe,
        deleteDocument,
        downloadDocument,
        shareDocument,
        updateDocument
    }
})