<script setup lang="ts">
import { ref, computed } from "vue";
import { useDocumentStore } from "@/store/documents";

const doc_store = useDocumentStore();
const search_query = ref("");
const selected_tags = ref<string[]>([]);

const all_tags = computed(() => {
    const tags = new Set<string>();
    doc_store.documents.forEach((doc) => {
        doc.tags.forEach((tag) => tags.add(tag));
    });
    return Array.from(tags);
});

const docs = computed(() => {
    return doc_store.documents;
});

console.log(docs);
</script>

<template>
    <v-container>
        <h1 class="text-h4 mb-6">Bibliothèque de documents</h1>

        <!-- Search and filters -->
        <v-card class="mb-6">
            <v-card-text>
                <v-row>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="search_query"
                            label="Rechercher un document"
                            prepend-inner-icon="mdi-magnify"
                            variant="outlined"
                            hide-details
                            clearable
                        ></v-text-field>
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-select
                            v-model="selected_tags"
                            :items="all_tags"
                            label="Filtrer par tags"
                            multiple
                            chips
                            variant="outlined"
                            hide-details
                        ></v-select>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <v-row>
            <v-col v-for="doc in docs" :key="doc.id" cols="12" md="6" lg="4">
                <v-card class="mx-auto" height="100%">
                    <v-card-title class="text-truncate">{{
                        doc.title
                    }}</v-card-title>
                    <v-card-subtitle>
                        <v-icon small class="mr-1">mdi-account</v-icon>
                        {{ doc.author }}
                        <span class="mx-1">•</span>
                        <v-icon small class="mr-1">mdi-calendar</v-icon>
                        {{ doc.upload_date }}
                    </v-card-subtitle>
                    <v-card-text>
                        <p class="mb-2">{{ doc.description }}</p>
                        <div class="d-flex align-center mb-2">
                            <v-icon small class="mr-1">mdi-download</v-icon>
                            <span>{{ doc.downloads }}</span>
                            <span class="mx-2">•</span>
                            <v-icon small class="mr-1">mdi-harddisk</v-icon>
                            <span>{{ `${doc.size} Mb` }}</span>
                        </div>
                        <div class="d-flex flex-wrap">
                            <v-chip
                                v-for="tag in doc.tags"
                                :key="tag"
                                size="small"
                                class="mr-1 mb-1"
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
                            <v-icon start>mdi-eye</v-icon>
                            Voir
                        </v-btn>
                        <v-btn
                            variant="text"
                            color="secondary"
                            :href="doc.path"
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

        <v-row v-if="docs.length == 0" class="mt-4">
            <v-col cols="12" class="text-center">
                <v-alert type="info">
                    Aucun document ne correspond à votre recherche.
                </v-alert>
            </v-col>
        </v-row>
    </v-container>
</template>
