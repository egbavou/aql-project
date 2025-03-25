<script setup lang="ts">
import {computed, onMounted, ref} from "vue";
import {useRoute, useRouter} from "vue-router";
import {useDocumentStore} from "@/store/documents";
import {clearFieldErrors, convertSize, formatDate} from "@/helpers";
import {useAuthStore} from "@/store/auth";
import toast from "@/plugins/toast";

const route = useRoute();
const router = useRouter();
const doc_store = useDocumentStore();
const auth_store = useAuthStore();
const doc_id: number = Number(route.params.id);
const show_delete_dialog = ref(false);
const show_share_dialog = ref(false);
const show_link_dialog = ref(false);
const share_link = ref("");
const copied = ref(false);

const form_valid = ref(true);
const form = ref({
    email: "",
});

const {form_errors, clearFieldError} = clearFieldErrors(doc_store, form);

const email_rules = [
    (value: string) => !!value || "L'email est requis",
    (value: string) => /.+@.+\..+/.test(value) || "L'email doit être valide",
];

async function shareDocument(id: number) {
    const success = await doc_store.shareDocument(id, {
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

async function deleteDocument(id: number) {
    const success = await doc_store.deleteDocument(id);

    if (success) {
        toast("Document supprimé.", "success");
        await router.push("/documents/created");
    }
}

async function generateDocLink(id: number) {
    const token = await doc_store.generateDocLink(id);

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

onMounted(async () => {
    await doc_store.getDocumentById(doc_id);
});

const doc = computed(() => doc_store.document);
const formattedDate = computed(() => formatDate(doc.value?.created_at || ""));
const formattedSize = computed(() => convertSize(doc.value?.size || 0));
const formattedVisibility = computed(() => ({
    public: "Public",
    private: "Privé",
    account_shared: "Partagé en privé",
    link_shared: "Partagé par lien"
}[doc.value?.visibility ?? "private"]));

const visibilityColor = computed(() => ({
    public: "green",
    private: "gray",
    account_shared: "blue",
    link_shared: "orange"
}[doc.value?.visibility ?? "private"]));
</script>

<template>
    <div v-if="!doc_store.loading">
        <v-container v-if="doc">
            <v-row>
                <v-col cols="12">
                    <v-btn
                        color="primary"
                        variant="text"
                        to="/documents/created"
                        class="mb-4"
                    >
                        <v-icon start>mdi-arrow-left</v-icon>
                        Retour aux documents
                    </v-btn>

                    <v-card>
                        <v-card-title class="text-h4 pa-4">
                            {{ doc.title }}
                            <v-chip :color="visibilityColor">
                                {{ formattedVisibility }}
                            </v-chip>
                        </v-card-title>

                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="8">
                                    <div class="d-flex flex-wrap mb-4">
                                        <v-chip
                                            v-for="tag in doc.tags"
                                            :key="tag.id"
                                            class="mr-2 mb-2"
                                        >
                                            {{ tag.name }}
                                        </v-chip>
                                    </div>

                                    <v-card
                                        variant="outlined"
                                        height="400px"
                                        class="d-flex align-center justify-center mb-4"
                                    >
                                        <div class="text-center">
                                            <v-icon size="64" color="grey"
                                            >mdi-file-document-outline
                                            </v-icon
                                            >
                                            <br/>
                                            <v-btn
                                                color="primary"
                                                class="mt-4"
                                                :loading="doc_store.loading2"
                                                @click="
                                                    doc_store.downloadDocument(
                                                        doc.id
                                                    )
                                                "
                                            >
                                                <v-icon start
                                                >mdi-download
                                                </v-icon
                                                >
                                                Télécharger
                                            </v-btn>
                                        </div>
                                    </v-card>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-card variant="outlined" class="mb-4">
                                        <v-card-title
                                        >Informations
                                        </v-card-title
                                        >
                                        <v-card-text>
                                            <v-list density="compact">
                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                        >mdi-account
                                                        </v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                    >Auteur
                                                    </v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                            doc.author
                                                        }}
                                                    </v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                        >mdi-calendar
                                                        </v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                    >Date de
                                                        téléversement
                                                    </v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                            formattedDate
                                                        }}
                                                    </v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                        >mdi-download
                                                        </v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                    >Nombre de
                                                        téléchargements
                                                    </v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                            doc.downloads
                                                        }}
                                                    </v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                        >mdi-harddisk
                                                        </v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title>
                                                        Taille
                                                    </v-list-item-title>
                                                    <v-list-item-subtitle>
                                                        {{ formattedSize }}
                                                    </v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                        >mdi-counter
                                                        </v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                    >Pages
                                                    </v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                            doc.pages
                                                        }}
                                                    </v-list-item-subtitle>
                                                </v-list-item>
                                            </v-list>
                                        </v-card-text>
                                    </v-card>

                                    <v-card variant="outlined" class="mb-4">
                                        <v-card-title>Actions</v-card-title>
                                        <v-card-text>
                                            <v-btn
                                                block
                                                color="primary"
                                                class="mb-2"
                                                :to="`/document/edit/${doc.id}`"
                                                v-if="
                                                    doc.user_id ==
                                                    Number(auth_store.user?.id)
                                                "
                                            >
                                                <v-icon start
                                                >mdi-pencil
                                                </v-icon
                                                >
                                                Modifier
                                            </v-btn>
                                            <v-btn
                                                block
                                                color="primary"
                                                class="mb-2"
                                                :loading="doc_store.loading2"
                                                @click="
                                                    doc_store.downloadDocument(
                                                        doc.id
                                                    )
                                                "
                                            >
                                                <v-icon start
                                                >mdi-download
                                                </v-icon
                                                >
                                                Télécharger
                                            </v-btn>
                                            <v-menu
                                                v-if="
                                                    doc.user_id ==
                                                    Number(auth_store.user?.id)
                                                "
                                            >
                                                <template
                                                    v-slot:activator="{ props }"
                                                >
                                                    <v-btn
                                                        block
                                                        color="secondary"
                                                        style="
                                                            color: white !important;
                                                        "
                                                        class="mb-2"
                                                        v-bind="props"
                                                    >
                                                        <v-icon start
                                                        >mdi-share-variant
                                                        </v-icon
                                                        >
                                                        Partager
                                                    </v-btn>
                                                </template>
                                                <v-list>
                                                    <v-list-item
                                                        @click="
                                                            show_share_dialog = true
                                                        "
                                                    >
                                                        <v-list-item-title
                                                        >A un
                                                            compte
                                                        </v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item>
                                                        <v-list-item-title
                                                            @click="
                                                                generateDocLink(
                                                                    Number(
                                                                        doc.id
                                                                    )
                                                                )
                                                            "
                                                            :loading="
                                                                doc_store.loading2
                                                            "
                                                        >Générer le
                                                            lien
                                                        </v-list-item-title
                                                        >
                                                    </v-list-item>
                                                </v-list>
                                            </v-menu>

                                            <v-btn
                                                block
                                                color="danger"
                                                @click="
                                                    show_delete_dialog = true
                                                "
                                                v-if="
                                                    doc.user_id ==
                                                    Number(auth_store.user?.id)
                                                "
                                            >
                                                <v-icon start
                                                >mdi-delete
                                                </v-icon
                                                >
                                                Supprimer
                                            </v-btn>
                                        </v-card-text>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-dialog v-model="show_delete_dialog" max-width="500px">
                <v-card>
                    <v-card-title
                    >Voulez-vous supprimer ce document ?
                    </v-card-title
                    >
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="grey"
                            variant="text"
                            @click="show_delete_dialog = false"
                        >Annuler
                        </v-btn
                        >
                        <v-btn
                            color="danger"
                            :loading="doc_store.loading2"
                            @click="deleteDocument(doc.id)"
                        >Supprimer
                        </v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="show_link_dialog" max-width="500px">
                <v-card>
                    <v-card-title>Copier le lien de partage</v-card-title>
                    <v-card-text>
                        <v-text-field
                            v-model="share_link"
                            readonly
                            variant="outlined"
                            append-inner-icon="mdi-content-copy"
                            @click:append-inner="copyToClipboard"
                        ></v-text-field>
                        <v-alert
                            v-if="copied"
                            type="success"
                            variant="outlined"
                            class="mt-2"
                        >
                            Lien de partage copié !
                        </v-alert>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="show_link_dialog = false"
                        >Fermer
                        </v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-dialog v-model="show_share_dialog" max-width="500px">
                <v-card>
                    <v-form
                        v-model="form_valid"
                        @submit.prevent="shareDocument(doc.id)"
                    >
                        <v-card-title
                        >Partager le document avec un compte
                        </v-card-title
                        >
                        <v-card-text>
                            <v-text-field
                                v-model="form.email"
                                :rules="email_rules"
                                label="Email du destinataire"
                                placeholder="Entrer l'email"
                                type="email"
                                variant="outlined"
                                class="mb-2"
                                :error-messages="
                                    form_errors.email
                                        ? form_errors.email[0]
                                        : ''
                                "
                                @update:modelValue="clearFieldError('email')"
                            ></v-text-field>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn
                                color="grey"
                                variant="text"
                                @click="show_share_dialog = false"
                            >Annuler
                            </v-btn
                            >
                            <v-btn
                                color="primary"
                                :loading="doc_store.loading2"
                                @click="shareDocument(doc.id)"
                            >Partager
                            </v-btn
                            >
                        </v-card-actions>
                    </v-form>
                </v-card>
            </v-dialog>
        </v-container>
    </div>
    <div class="mt-5" v-else>
        <v-row justify="center">
            <v-progress-circular
                indeterminate
                color="primary"
                size="64"
            ></v-progress-circular>
        </v-row>
    </div>
</template>
