import axios from "axios";

export function axiosError(error: any) {
    if (axios.isAxiosError(error) && error.response) {
        const { status, data } = error.response;

        if (status === 422) {
            return { status: "422", errors: data.errors };
        } else if (status === 419) {
            return { status: "419", message: "Page expirée. Veuillez actualiser la page" };
        } else if (status === 401) {
            return { status: "401", message: "Email ou mot de passe incorrect" };
        }
    }

    return { status: "none", message: "Une erreur s'est produite. Veuillez réessayer" };
}

export function convertSize(size: number) {
    const units = ["O", "Ko", "Mo", "Go", "To"];
    let i = 0;
    while (size >= 1024 && i < units.length - 1) {
        size /= 1024;
        i++;
    }
    return size.toFixed(2) + " " + units[i];
}

export function formatDate(date: string) {
    const d = new Date(date);
    const options: Intl.DateTimeFormatOptions = { year: 'numeric', month: '2-digit', day: '2-digit' }
    return d.toLocaleDateString('fr-FR', options);
}

export function downloadFile(
    file_name: string,
    data: string | ArrayBuffer | ArrayBufferView | Blob,
    mime = "text/plain",
    bom?: string | Uint8Array,
) {
    const blob_data = bom === undefined ? [data] : [bom, data]
    const blob = new Blob(blob_data, { type: mime })
    const a = document.createElement("a")

    a.download = file_name
    a.href = URL.createObjectURL(blob)
    a.click()
    setTimeout(() => {
        URL.revokeObjectURL(a.href)
        a.remove()
    }, 200)
}
