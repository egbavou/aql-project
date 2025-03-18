<script setup lang="ts">
import { ref, computed, onMounted, watch } from "vue";
import { useDocumentStore } from "@/store/documents";
import DocumentCard from "@/components/DocumentCard.vue";

const doc_store = useDocumentStore();

const search_query_title = ref("");
const search_query_author = ref("");
const selected_languages = ref<string | null>(null);
const selected_tag = ref<number>();

const all_languages = ref([
    { code: "fr", name: "Français" },
    { code: "en", name: "Anglais" },
    { code: "es", name: "Espagnol" },
]);

function onPageChange() {
    doc_store.getDocuments();
}

async function fetchDocuments() {
    await doc_store.getDocuments(doc_store.current_page, {
        title: search_query_title.value,
        author: search_query_author.value,
        language: selected_languages.value,
        tag: selected_tag.value,
    });
}

watch(
    [search_query_title, search_query_author, selected_languages, selected_tag],
    fetchDocuments
);

onMounted(async () => {
    await doc_store.getDocumentsTags();
    await fetchDocuments();
});

const docs = computed(() => doc_store.documents);
const tags = computed(() => doc_store.tags);
</script>

<template>
    <v-container fluid>
        <v-row>
            <v-col cols="12" md="3" class="d-flex flex-column">
                <v-sheet
                    class="pa-4"
                    elevation="2"
                    style="position: sticky; top: 16px"
                >
                    <h2 class="text-h6 mb-4">Recherche avancée</h2>
                    <v-text-field
                        v-model="search_query_title"
                        label="Par titre"
                        prepend-inner-icon="mdi-magnify"
                        variant="outlined"
                        hide-details
                        clearable
                    ></v-text-field
                    ><br />
                    <v-text-field
                        v-model="search_query_author"
                        label="Par auteur"
                        prepend-inner-icon="mdi-account"
                        variant="outlined"
                        hide-details
                        clearable
                    ></v-text-field
                    ><br />
                    <v-select
                        v-model="selected_tag"
                        :items="tags"
                        item-title="name"
                        item-value="id"
                        label="Filtrer par tag"
                        variant="outlined"
                        hide-details
                    ></v-select
                    ><br />
                    <v-select
                        v-model="selected_languages"
                        :items="all_languages"
                        item-title="name"
                        item-value="code"
                        label="Filtrer par langue"
                        variant="outlined"
                        hide-details
                    ></v-select>
                </v-sheet>
            </v-col>

            <v-col cols="12" md="9">
                <h1 class="text-h4 mb-6">Bibliothèque de documents</h1>

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
                        <v-alert type="info"
                            >Aucun document ne correspond à votre
                            recherche.</v-alert
                        >
                    </v-col>
                </v-row>

                <v-row justify="center" v-if="!doc_store.loading">
                    <v-pagination
                        v-model="doc_store.current_page"
                        :length="doc_store.last_page"
                        :total-visible="5"
                        @click="onPageChange"
                    ></v-pagination>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>
