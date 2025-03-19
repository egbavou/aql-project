import { defineStore } from 'pinia'
import { ref } from 'vue'
import axiosUser from '@/axios';
import { axiosError, downloadFile } from '@/helpers';

export interface DocumentData {
    title: string;
    author: string;
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
    const documents = ref<Document[]>([]);
    const document = ref<Document>();
    const current_page = ref(1);
    const last_page = ref(1);
    const total = ref(0);
    const tags = ref<Tag[]>([])

    async function getDocuments(page: number = current_page.value, filters: any = {}) {
        loading.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const per_page: number = 18;
            const response = await axiosUser.get(`/api/documents`, {
                params: {
                    page: page,
                    per_page: per_page,
                    title: filters.title || "",
                    author: filters.author || "",
                    language: filters.language || "",
                    tag: Number(filters.tag) || "",
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

    async function getDocumentsMe(page: number = current_page.value, cat: string = "created", filters: any = {}) {
        loading.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const per_page: number = 18;
            // const page: number = current_page.value
            const response = await axiosUser.get(`/api/documents/${cat}`, {
                params: {
                    page: page,
                    per_page: per_page,
                    title: filters.title || "",
                    author: filters.author || "",
                    language: filters.language || "",
                    tag: Number(filters.tag) || "",
                }
            });

            loading.value = false

            documents.value = response.data.data
            current_page.value = response.data.current_page
            last_page.value = response.data.last_page
            total.value = response.data.total

            return response.data.data
        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function addDocument(formdata: DocumentData): Promise<[boolean, string, number]> {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Content-Type": "multipart/form-data"
            }

            await axiosUser.get(`/sanctum/csrf-cookie`);

            const response = await axiosUser.post(`/api/documents`, formdata, {
                headers: headers
            });

            loading.value = false;

            if (response.status == 200) return [true, response.data.title, response.data.id]

        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return [false, "erreur", 1]
        }

        return [false, "erreur", 1];
    }

    async function updateDocument(id: number, formdata: DocumentData): Promise<[boolean, string, number]> {
        loading.value = true
        errors.value = null
        try {
            let headers = {
                "Content-Type": "multipart/form-data",
            }
            await axiosUser.get(`/sanctum/csrf-cookie`);

            const response = await axiosUser.post(`/api/documents/${id}`, formdata, {
                headers: headers
            });

            loading.value = false;

            if (response.status == 200) return [true, response.data.title, response.data.id]

        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return [false, "erreur", 1]
        }

        return [false, "erreur", 1]
    }

    async function getDocumentById(id: number) {
        loading.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const response = await axiosUser.get(`/api/documents/${id}`);

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

            await axiosUser.get(`${import.meta.env.VITE_API_URL}/sanctum/csrf-cookie`);
            const response = await axiosUser.delete(`/api/documents/${id}`);

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

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const response = await axiosUser.get(`/api/documents/download/${id}`, {
                responseType: "blob"
            });

            loading2.value = false
            const filename = `DocShare_${Date.now()}`

            return downloadFile(filename, response.data, "application/pdf");
        } catch (error) {
            loading2.value = false
            errors.value = axiosError(error)
        }
    }

    async function shareDocument(id: number, formdata: ShareDocumentData) {
        loading2.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            await axiosUser.post(`/api/documents/${id}/private-share`, formdata);

            loading2.value = false

            return true

        } catch (error) {
            loading2.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function generateDocLink(id: number) {
        loading2.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const response = await axiosUser.post(`/api/documents/${id}/link-share`, {});

            loading2.value = false

            if (response.data) {
                return response.data.token;
            }

        } catch (error) {
            loading2.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function getDocByToken(token: string) {
        loading.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const response = await axiosUser.get(`/api/documents/token/${token}`);

            loading.value = false

            if (response.data) {
                document.value = response.data;
                return response.data
            }

        } catch (error) {
            loading.value = false
            errors.value = axiosError(error)

            return false
        }
    }

    async function getDocumentsTags() {
        loading.value = true
        errors.value = null
        try {

            await axiosUser.get(`/sanctum/csrf-cookie`);
            const response = await axiosUser.get('/api/tags');

            loading.value = false

            tags.value = response.data

            return response.data
        } catch (error) {
            loading.value = false
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
        tags,
        getDocumentById,
        addDocument,
        getDocuments,
        getDocumentsMe,
        deleteDocument,
        downloadDocument,
        shareDocument,
        generateDocLink,
        updateDocument,
        getDocByToken,
        getDocumentsTags
    }
})
