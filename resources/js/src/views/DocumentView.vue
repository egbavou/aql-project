<script setup lang="ts">
import { ref, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useDocumentStore } from "@/store/documents";

const route = useRoute();
const router = useRouter();
const doc_store = useDocumentStore();
const doc_id: number = route.params.id;

const document = computed(() => {
    const doc = doc_store.getDocumentById(Number.parseInt(doc_id));
    if (!doc) {
        router.push("/documents");
    }
    return doc;
});
</script>

<template>
    <v-container v-if="document">
        <v-row>
            <v-col cols="12">
                <v-btn
                    color="primary"
                    variant="text"
                    to="/documents"
                    class="mb-4"
                >
                    <v-icon start>mdi-arrow-left</v-icon>
                    Retour aux documents
                </v-btn>

                <v-card>
                    <v-card-title class="text-h4 pa-4">
                        {{ document.title }}
                    </v-card-title>

                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="8">
                                <p class="text-body-1 mb-4">
                                    {{ document.description }}
                                </p>

                                <v-divider class="mb-4"></v-divider>

                                <div class="d-flex flex-wrap mb-4">
                                    <v-chip
                                        v-for="tag in document.tags"
                                        :key="tag"
                                        class="mr-2 mb-2"
                                    >
                                        {{ tag }}
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
                                        <p class="mt-2">Aperçu du document</p>
                                        <v-btn
                                            color="primary"
                                            class="mt-4"
                                            :href="document.path"
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
                                    <v-card-title>Informations</v-card-title>
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
                                                    document.author
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
                                                    document.upload_date
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
                                                    document.downloads
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
                                                    document.size
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
                                            :href="document.path"
                                            target="_blank"
                                        >
                                            <v-icon start>mdi-download</v-icon>
                                            Télécharger
                                        </v-btn>

                                        <v-btn
                                            block
                                            color="secondary"
                                            class="mb-2"
                                        >
                                            <v-icon start
                                                >mdi-share-variant</v-icon
                                            >
                                            Partager
                                        </v-btn>
                                    </v-card-text>
                                </v-card>
                            </v-col>
                        </v-row>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
