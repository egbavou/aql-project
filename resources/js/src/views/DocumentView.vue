<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useDocumentStore } from "@/store/documents";
import { formatDate, convertSize } from "@/helpers";
import { useAuthStore } from "@/store/auth";
import toast from "@/plugins/toast";

const route = useRoute();
const router = useRouter();
const doc_store = useDocumentStore();
const auth_store = useAuthStore();
const doc_id: number = route.params.id;
const show_delete_dialog = ref(false);
const show_share_dialog = ref(false);

const form_valid = ref(true);
const form = ref({
    email: "",
});

const form_errors = computed(() => doc_store.errors?.errors || {});

const clearFieldError = (field: string) => {
    if (form_errors.value[field]) {
        delete doc_store.errors.errors[field];
    }
};

watch(
    form,
    (new_form) => {
        Object.keys(new_form).forEach((field) => {
            if (form_errors.value[field]) {
                delete doc_store.errors.errors[field];
            }
        });
    },
    { deep: true }
);

const email_rules = [
    (value: string) => !!value || "L'email est requis",
    (value: string) => /.+@.+\..+/.test(value) || "L'email doit être valide",
];

async function shareDocument(id: number) {
    const success = await doc_store.shareDocument(id, {
        email: form.value.email,
    });

    if (doc_store.errors) {
        if (["419", "401", "none"].includes(doc_store.errors.status)) {
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
        router.push("/documents/created");
    }
}

onMounted(async () => {
    await doc_store.getDocumentById(doc_id);
});

const doc = computed(() => doc_store.document);
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
                                                >mdi-file-document-outline</v-icon
                                            >
                                            <br />
                                            <v-btn
                                                color="primary"
                                                class="mt-4"
                                                target="_blank"
                                            >
                                                <v-icon start
                                                    >mdi-open-in-new</v-icon
                                                >
                                                Ouvrir le document
                                            </v-btn>
                                        </div>
                                    </v-card>
                                </v-col>

                                <v-col cols="12" md="4">
                                    <v-card variant="outlined" class="mb-4">
                                        <v-card-title
                                            >Informations</v-card-title
                                        >
                                        <v-card-text>
                                            <v-list density="compact">
                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                            >mdi-account</v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                        >Auteur</v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                        doc.author
                                                    }}</v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                            >mdi-calendar</v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                        >Date de
                                                        téléversement</v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                        formatDate(
                                                            doc.created_at
                                                        )
                                                    }}</v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                            >mdi-download</v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                        >Nombre de
                                                        téléchargement</v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                        doc.downloads
                                                    }}</v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                            >mdi-harddisk</v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                        >Taille</v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                        convertSize(doc.size)
                                                    }}</v-list-item-subtitle>
                                                </v-list-item>

                                                <v-list-item>
                                                    <template v-slot:prepend>
                                                        <v-icon color="primary"
                                                            >mdi-counter</v-icon
                                                        >
                                                    </template>
                                                    <v-list-item-title
                                                        >Pages</v-list-item-title
                                                    >
                                                    <v-list-item-subtitle>{{
                                                        doc.pages
                                                    }}</v-list-item-subtitle>
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
                                                    >mdi-pencil</v-icon
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
                                                    >mdi-download</v-icon
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
                                                            >mdi-share-variant</v-icon
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
                                                            >Compte</v-list-item-title
                                                        >
                                                    </v-list-item>
                                                    <v-list-item>
                                                        <v-list-item-title
                                                            >Lien</v-list-item-title
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
                                                    >mdi-delete</v-icon
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
                        >Voulez-vous supprimer ce document ?</v-card-title
                    >
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="grey"
                            variant="text"
                            @click="show_delete_dialog = false"
                            >Annuler</v-btn
                        >
                        <v-btn
                            color="danger"
                            :loading="doc_store.loading2"
                            @click="deleteDocument(doc.id)"
                            >Supprimer</v-btn
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
                            >Partager le document avec un compte</v-card-title
                        >
                        <v-card-text>
                            <v-text-field
                                v-model="form.email"
                                :rules="email_rules"
                                label="Email"
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
                                >Annuler</v-btn
                            >
                            <v-btn
                                color="primary"
                                :loading="doc_store.loading2"
                                @click="shareDocument(doc.id)"
                                >Partager</v-btn
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
