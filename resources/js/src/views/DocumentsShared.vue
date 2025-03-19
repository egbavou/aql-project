<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useDocumentStore } from "@/store/documents";
import DocumentCard from "@/components/DocumentCard.vue";
import FiltreDocForm from "@/components/FiltreDocForm.vue";

const doc_store = useDocumentStore();

const search_query_title = ref("");
const search_query_author = ref("");
const selected_language = ref<string>();
const selected_tag = ref<number>();

async function fetchDocuments() {
    await doc_store.getDocumentsMe(doc_store.current_page, "shared", {
        title: search_query_title.value,
        author: search_query_author.value,
        language: selected_language.value,
        tag: selected_tag.value,
    });
}

watch(
    [search_query_title, search_query_author, selected_language, selected_tag],
    fetchDocuments
);

onMounted(async () => {
    await fetchDocuments();
});

const docs = computed(() => doc_store.documents);
</script>

<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" md="3" class="d-flex flex-column">
                <FiltreDocForm
                    v-model:search_title="search_query_title"
                    v-model:search_author="search_query_author"
                    v-model:selected_language="selected_language"
                    v-model:selected_tag="selected_tag"
                    @fetchDocuments="fetchDocuments"
                />
            </v-col>

            <v-col cols="12" md="9">
                <h1 class="text-h4 mb-6">Documents partagés avec moi</h1>

                <v-row v-if="!doc_store.loading">
                    <v-col
                        v-for="doc in docs"
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
                    v-if="!doc_store.loading && docs.length == 0"
                    class="mt-4"
                >
                    <v-col cols="12" class="text-center">
                        <v-alert type="info">
                            Aucun document ne correspond à votre recherche.
                        </v-alert>
                    </v-col>
                </v-row>

                <v-row justify="center" v-if="!doc_store.loading">
                    <v-pagination
                        v-model="doc_store.current_page"
                        :length="doc_store.last_page"
                        :total-visible="5"
                        @click="fetchDocuments"
                    ></v-pagination>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>
