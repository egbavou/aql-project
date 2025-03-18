<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useDocumentStore } from "@/store/documents";
import DocumentCard from "@/components/DocumentCard.vue";

const doc_store = useDocumentStore();
const search_query = ref("");
const selected_tags = ref<string[]>([]);

const all_tags = ["a", "b", "c"];

function onPageChange() {
    doc_store.getDocumentsMe("shared");
}

onMounted(async () => {
    await doc_store.getDocumentsMe("shared");
});

const docs = computed(() => doc_store.documents);
</script>

<template>
    <v-container>
        <h1 class="text-h4 mb-6">Documents partagés</h1>

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
        <v-row v-if="!doc_store.loading">
            <v-col v-for="doc in docs" :key="doc.id" cols="12" md="6" lg="4">
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

        <v-row v-if="!doc_store.loading && docs.length == 0" class="mt-4">
            <v-col cols="12" class="text-center">
                <v-alert type="info"> Aucun document trouvé. </v-alert>
            </v-col>
        </v-row>
        <v-row justify="center" v-if="!doc_store.loading && docs.length > 0">
            <v-pagination
                v-model="doc_store.current_page"
                :length="doc_store.last_page"
                :total-visible="5"
                @click="onPageChange"
            ></v-pagination>
        </v-row>
    </v-container>
</template>
