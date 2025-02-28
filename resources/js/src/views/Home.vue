<script setup lang="ts">
import { useRouter } from "vue-router";
import { useDocumentStore } from "@/store/documents";

const router = useRouter();
const doc_store = useDocumentStore();
const recent_documents = doc_store.documents.slice(0, 3);

function navigateToDocuments() {
    router.push("/documents");
}

function navigateToUpload() {
    router.push("/upload");
}
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
                            @click="navigateToDocuments"
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
                            @click="navigateToUpload"
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
                <v-row>
                    <v-col
                        v-for="doc in recent_documents"
                        :key="doc.id"
                        cols="12"
                        md="4"
                    >
                        <v-card class="mx-auto" height="100%">
                            <v-card-title>{{ doc.title }}</v-card-title>
                            <v-card-subtitle
                                >{{ doc.author }} •
                                {{ doc.upload_date }}</v-card-subtitle
                            >
                            <v-card-text>
                                <p>{{ doc.description }}</p>
                                <div class="d-flex mt-2">
                                    <v-chip
                                        v-for="tag in doc.tags"
                                        :key="tag"
                                        size="small"
                                        class="mr-1"
                                    >
                                        {{ tag }}
                                    </v-chip>
                                </div>
                            </v-card-text>
                            <v-card-actions>
                                <v-btn
                                    variant="text"
                                    color="primary"
                                    :to="`/document/${doc.id}`"
                                >
                                    Voir le document
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

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
