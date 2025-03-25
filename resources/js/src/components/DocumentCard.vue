<script setup lang="ts">
import { ref } from "vue";
import { clearFieldErrors, convertSize, formatDate } from "@/helpers";
import { useAuthStore } from "@/store/auth";
import { useDocumentStore } from "@/store/documents";
import toast from "@/plugins/toast";

const props = defineProps<{
    doc: {
        id: number;
        user_id: number;
        title: string;
        author: string;
        created_at: string;
        downloads: number;
        size: number;
        tags: { id: number; name: string }[];
    };
}>();

const auth_store = useAuthStore();
const doc_store = useDocumentStore();

const show_share_dialog = ref(false);
const show_link_dialog = ref(false);
const share_link = ref("");
const copied = ref(false);

const form_valid = ref(true);
const form = ref({
    email: "",
});

const { form_errors, clearFieldError } = clearFieldErrors(doc_store, form);

const email_rules = [
    (value: string) => !!value || "L'email est requis",
    (value: string) => /.+@.+\..+/.test(value) || "L'email doit être valide",
];

async function shareDocument() {
    const success = await doc_store.shareDocument(props.doc.id, {
        email: form.value.email,
    });

    if (doc_store.errors) {
        if (["419", "401", "500", "none"].includes(doc_store.errors.status)) {
            toast(doc_store.errors.message, "error");
        }
    }

    if (success) {
        show_share_dialog.value = false;
        toast("Le document a été partagé.", "success");
    }
}

async function generateDocLink() {
    const token = await doc_store.generateDocLink(props.doc.id);

    if (token) {
        share_link.value = `${
            import.meta.env.VITE_API_URL
        }/document/${token}/share`;
        toast("Le lien a été généré.", "success");
        show_link_dialog.value = true;
    }
}

async function copyToClipboard() {
    await navigator.clipboard.writeText(share_link.value);
    copied.value = true;
    setTimeout(() => (copied.value = false), 2000);
}
</script>

<template>
    <v-card class="mx-auto" height="100%">
        <v-card-title class="text-truncate">{{ doc.title }}</v-card-title>
        <v-card-subtitle>
            <v-icon small class="mr-1">mdi-account</v-icon>
            {{ doc.author }}
            <span class="mx-1">•</span>
            <v-icon small class="mr-1">mdi-calendar</v-icon>
            {{ formatDate(doc.created_at) }}
        </v-card-subtitle>
        <v-card-text>
            <div class="d-flex align-center mb-2">
                <v-icon small class="mr-1">mdi-download</v-icon>
                <span>{{ doc.downloads }}</span>
                <span class="mx-2">•</span>
                <v-icon small class="mr-1">mdi-harddisk</v-icon>
                <span>{{ convertSize(doc.size) }}</span>
            </div>
            <div class="d-flex flex-wrap">
                <v-chip
                    v-for="tag in doc.tags"
                    :key="tag.id"
                    size="small"
                    class="mr-1 mb-1"
                >
                    {{ tag.name }}
                </v-chip>
            </div>
        </v-card-text>
        <v-card-actions>
            <v-btn variant="text" color="primary" :to="`/document/${doc.id}`">
                <v-icon start>mdi-eye</v-icon> Voir
            </v-btn>
            <v-btn
                variant="text"
                color="secondary"
                :loading="doc_store.loading2"
                @click="doc_store.downloadDocument(doc.id)"
            >
                <v-icon start>mdi-download</v-icon> Télécharger
            </v-btn>
            <v-menu v-if="doc.user_id == Number(auth_store.user?.id)">
                <template v-slot:activator="{ props }">
                    <v-btn style="color: black !important" v-bind="props">
                        <v-icon start>mdi-share-variant</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item @click="show_share_dialog = true">
                        <v-list-item-title>A un compte</v-list-item-title>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-title
                            @click="generateDocLink"
                            :loading="doc_store.loading2"
                        >
                            Générer le lien
                        </v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-card-actions>
    </v-card>

    <v-dialog v-model="show_share_dialog" max-width="500px">
        <v-card>
            <v-card-title>Partager le document</v-card-title>
            <v-card-text>
                <v-form v-model="form_valid">
                    <v-text-field
                        v-model="form.email"
                        label="Email du destinataire"
                        :rules="email_rules"
                        required
                        placeholder="Entrer l'email"
                        type="email"
                        variant="outlined"
                        class="mb-2"
                        :error-messages="
                            form_errors.email ? form_errors.email[0] : ''
                        "
                        @update:modelValue="clearFieldError('email')"
                    ></v-text-field>
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn color="grey" @click="show_share_dialog = false">
                    Annuler
                </v-btn>
                <v-btn
                    color="primary"
                    :loading="doc_store.loading2"
                    @click="shareDocument"
                >
                    Envoyer
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

    <v-dialog v-model="show_link_dialog" max-width="500px">
        <v-card>
            <v-card-title>Lien de partage généré</v-card-title>
            <v-card-text>
                <v-text-field
                    v-model="share_link"
                    readonly
                    append-icon="mdi-content-copy"
                    @click:append="copyToClipboard"
                ></v-text-field>
                <span v-if="copied" class="text-success">Copié !</span>
            </v-card-text>
            <v-card-actions>
                <v-btn color="grey" @click="show_link_dialog = false">
                    Fermer
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
