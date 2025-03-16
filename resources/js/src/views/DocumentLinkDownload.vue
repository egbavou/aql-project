<script setup lang="ts">
import { computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useDocumentStore } from "@/store/documents";
import { formatDate, convertSize } from "@/helpers";

const route = useRoute();
const doc_store = useDocumentStore();
const token: string = route.params.token;

onMounted(async () => {
    await doc_store.getDocByToken(token);
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
                        to="/documents"
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
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
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
