<script setup lang="ts">
import { useRouter } from "vue-router";
import { useDocumentStore } from "@/store/documents";
import { formatDate, convertSize } from "@/helpers";
import { computed, onMounted } from "vue";

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
                        <v-card class="mx-auto" height="100%">
                            <v-card-title class="text-truncate">{{
                                doc.title
                            }}</v-card-title>
                            <v-card-subtitle>
                                <v-icon small class="mr-1">mdi-account</v-icon>
                                {{ doc.author }}
                                <span class="mx-1">•</span>
                                <v-icon small class="mr-1">mdi-calendar</v-icon>
                                {{ formatDate(doc.created_at) }}
                            </v-card-subtitle>
                            <v-card-text>
                                <div class="d-flex align-center mb-2">
                                    <v-icon small class="mr-1"
                                        >mdi-download</v-icon
                                    >
                                    <span>{{ doc.downloads }}</span>
                                    <span class="mx-2">•</span>
                                    <v-icon small class="mr-1"
                                        >mdi-harddisk</v-icon
                                    >
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
                                <v-btn
                                    variant="text"
                                    color="primary"
                                    :to="`/document/${doc.id}`"
                                >
                                    <v-icon start>mdi-eye</v-icon>
                                    Voir
                                </v-btn>
                                <v-btn
                                    variant="text"
                                    color="secondary"
                                    target="_blank"
                                >
                                    <v-icon start>mdi-download</v-icon>
                                    Télécharger
                                </v-btn>
                                <v-spacer></v-spacer>
                                <v-btn icon>
                                    <v-icon>mdi-share-variant</v-icon>
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row v-else justify="center">
                    <v-progress-circular
                        indeterminate
                        color="primary"
                        size="64"
                    ></v-progress-circular>
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
