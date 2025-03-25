<script setup lang="ts">
import { useRouter } from "vue-router";
import { useDocumentStore } from "@/store/documents";
import { computed, onMounted } from "vue";
import DocumentCard from "@/components/DocumentCard.vue";

const router = useRouter();
const doc_store = useDocumentStore();

onMounted(async () => {
    await doc_store.getDocuments();
});

const recent_docs = computed(() => doc_store.documents.slice(0, 6));
</script>

<template>
    <v-container>
        <v-row class="mt-8">
            <v-col cols="12" class="text-center">
                <h1 class="text-h3 font-weight-bold mb-6">DocShare</h1>
                <p class="text-subtitle-1 mb-8">
                    Gérez, partagez et collaborez sur vos mémoires et documents
                    importants
                </p>

                <v-row justify="center" class="mb-10">
                    <v-col cols="12" sm="6" md="4">
                        <v-btn
                            color="primary"
                            size="x-large"
                            block
                            to="/documents"
                        >
                            <v-icon start>mdi-file-document-multiple</v-icon>
                            Parcourir les documents
                        </v-btn>
                    </v-col>
                    <v-col cols="12" sm="6" md="4">
                        <v-btn
                            style="color: white !important"
                            color="secondary"
                            size="x-large"
                            block
                            to="/document/add"
                        >
                            <v-icon start>mdi-upload</v-icon>
                            Téléverser un document
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-divider class="my-8"></v-divider>

        <v-row>
            <v-col cols="12">
                <h2 class="text-h5 mb-4">Documents récents</h2>
                <v-row v-if="!doc_store.loading">
                    <v-col
                        v-for="doc in recent_docs"
                        :key="doc.id"
                        cols="12"
                        md="6"
                        lg="4"
                    >
                        <DocumentCard :doc="doc" />
                    </v-col>
                </v-row>
                <v-row v-else justify="center">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="64"
                    ></v-progress-circular>
                </v-row>
                <v-row
                    v-if="!doc_store.loading && recent_docs.length == 0"
                    class="mt-4"
                >
                    <v-col cols="12" class="text-center">
                        <v-alert type="info">
                            Aucun document ou connectez-vous pour en disposer.
                        </v-alert>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-divider class="my-8"></v-divider>

        <v-row class="mt-10">
            <v-col cols="12" md="4">
                <v-card class="pa-4 h-100">
                    <v-icon size="x-large" color="primary" class="mb-4"
                        >mdi-file-upload</v-icon
                    >
                    <h3 class="text-h6 mb-2">Téléversement facile</h3>
                    <p>
                        Téléversez vos mémoires et documents en quelques clics.
                        Prise en charge de multiples formats.
                    </p>
                </v-card>
            </v-col>
            <v-col cols="12" md="4">
                <v-card class="pa-4 h-100">
                    <v-icon size="x-large" color="primary" class="mb-4"
                        >mdi-share-variant</v-icon
                    >
                    <h3 class="text-h6 mb-2">Partage sécurisé</h3>
                    <p>
                        Partagez vos documents avec des collègues ou des groupes
                        spécifiques en toute sécurité.
                    </p>
                </v-card>
            </v-col>
            <v-col cols="12" md="4">
                <v-card class="pa-4 h-100">
                    <v-icon size="x-large" color="primary" class="mb-4"
                        >mdi-magnify</v-icon
                    >
                    <h3 class="text-h6 mb-2">Recherche avancée</h3>
                    <p>
                        Retrouvez rapidement vos documents grâce à notre système
                        de recherche et de filtrage avancé.
                    </p>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
