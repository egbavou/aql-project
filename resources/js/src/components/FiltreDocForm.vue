<script setup lang="ts">
import {computed, ref} from "vue";
import {useDocumentStore} from "@/store/documents";

const doc_store = useDocumentStore();

const props = defineProps({
    search_title: String,
    search_author: String,
    selected_language: String,
    selected_tag: Number,
});

const emit = defineEmits([
    "update:search_title",
    "update:search_author",
    "update:selected_language",
    "update:selected_tag",
    "fetchDocuments",
]);

const all_languages = ref([
    { code: "fr", name: "Français" },
    { code: "en", name: "Anglais" },
    { code: "es", name: "Espagnol" },
]);

const tags = computed(() => doc_store.tags);

if (!tags.value.length) {
    doc_store.getDocumentsTags();
}
</script>

<template>
    <v-sheet class="pa-4" elevation="2" style="position: sticky; top: 16px">
        <h2 class="text-h6 mb-4">Recherche avancée</h2>

        <v-text-field
            :model-value="search_title"
            @update:model-value="(value) => emit('update:search_title', value)"
            label="Par titre"
            prepend-inner-icon="mdi-magnify"
            variant="outlined"
            hide-details
            clearable
            class="mb-4"
        ></v-text-field>

        <v-text-field
            :model-value="search_author"
            @update:model-value="(value) => emit('update:search_author', value)"
            label="Par auteur"
            prepend-inner-icon="mdi-account"
            variant="outlined"
            hide-details
            clearable
            class="mb-4"
        ></v-text-field>

        <v-select
            :model-value="selected_tag"
            @update:model-value="(value) => emit('update:selected_tag', value)"
            :items="tags"
            item-title="name"
            item-value="id"
            label="Filtrer par tag"
            variant="outlined"
            hide-details
            class="mb-4"
        ></v-select>

        <v-select
            :model-value="selected_language"
            @update:model-value="
                (value) => emit('update:selected_language', value)
            "
            :items="all_languages"
            item-title="name"
            item-value="code"
            label="Filtrer par langue"
            variant="outlined"
            hide-details
        ></v-select>
    </v-sheet>
</template>
