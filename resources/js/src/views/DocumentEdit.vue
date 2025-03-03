<script setup lang="ts">
import toast from "@/plugins/toast";
import { useDocumentStore } from "@/store/documents";
import { computed, onMounted, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";

const route = useRoute();
const router = useRouter();
const doc_store = useDocumentStore();

const doc_id = route.params.id;

const languages = ref([
    { code: "fr", name: "Français" },
    { code: "en", name: "Anglais" },
    { code: "es", name: "Espagnol" },
]);

const visibilities = ref([
    { code: "public", name: "Publique" },
    { code: "private", name: "Privé" },
]);

const form = ref({
    title: "",
    author: "",
    pages: "",
    language: "",
    visibility: "",
    tags: [],
    file: null,
});

const title_rule = [(value: string) => !!value || "Le titre est requis"];
const author_rule = [
    (value: string) => !!value || "Le nom de l'auteur est requis",
];
const pages_rule = [
    (value: number) => !!value || "Le nombre de pages est requis",
    (value: number) => value > 0 || "Le nombre de page doit être supérieur à 0",
];
const language_rule = [
    (value: string) => !!value || "La langue du contenu est requis",
];
const visibility_rule = [
    (value: string) => !!value || "La visibilité est requise",
];

const max_size = 20480 * 1024;
const file_rule = [
    (value: File | null) =>
        (value && value.type === "application/pdf") ||
        "Le fichier doit être au format PDF",
    (value: File | null) =>
        (value && value.size <= max_size) ||
        "Le fichier ne doit pas dépasser 20 Mo",
];

const tag_input = ref("");
const form_valid = ref(true);

function addTag() {
    const tag = tag_input.value.trim();
    if (tag && !form.value.tags.includes(tag)) {
        form.value.tags.push(tag);
    }
    tag_input.value = "";
}

function removeTag(tag: string) {
    form.value.tags = form.value.tags.filter((t) => t !== tag);
}

function onFileChange(event: Event) {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        form.value.file = target.files[0];
    } else {
        form.value.file = null;
    }
}

const form_errors = computed(() => doc_store.errors?.errors || {});

const clearFieldError = (field: string) => {
    if (form_errors.value[field]) {
        delete doc_store.errors.errors[field];
    }
};

watch(
    form,
    (new_form) => {
        Object.keys(new_form).forEach((field) => {
            if (form_errors.value[field]) {
                delete doc_store.errors.errors[field];
            }
        });
    },
    { deep: true }
);

async function updateDocument(id: number) {
    if (!form_valid.value) return;

    const [success, title] = await doc_store.updateDocument(id, {
        title: form.value.title,
        author: form.value.author,
        pages: Number.parseInt(form.value.pages),
        language: form.value.language,
        visibility: form.value.visibility,
        tags: form.value.tags,
        file: form.value.file,
    });

    if (doc_store.errors) {
        if (
            doc_store.errors.status == "419" ||
            doc_store.errors.status == "401" ||
            doc_store.errors.status == "none"
        ) {
            toast(doc_store.errors.message, "error");
        }
    }
    if (success) {
        toast(`Document: ${title} modifié.`, "success");
    }
}

onMounted(async () => {
    await doc_store.getDocumentById(doc_id);
    if (doc_store.document) {
        form.value = {
            title: doc_store.document.title,
            author: doc_store.document.author,
            pages: doc_store.document.pages,
            language: doc_store.document.language,
            visibility: doc_store.document.visibility,
            tags: doc_store.document.tags?.map((tag) => tag.name) || [],
            file: null,
        };
    }
});

const doc = computed(() => doc_store.document);
</script>

<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12" md="8">
                <h1 class="text-h4 mb-6">Modification de {{ doc?.title }}</h1>

                <v-card>
                    <v-card-text>
                        <v-form
                            v-model="form_valid"
                            @submit.prevent="
                                updateDocument(Number.parseInt(doc?.id))
                            "
                        >
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="form.title"
                                        label="Titre du document *"
                                        :rules="title_rule"
                                        required
                                        variant="outlined"
                                        :error-messages="
                                            form_errors.title
                                                ? form_errors.title[0]
                                                : ''
                                        "
                                        @update:modelValue="
                                            clearFieldError('title')
                                        "
                                        :class="form_errors.title ? 'mb-3' : ''"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.author"
                                        label="Auteur *"
                                        :rules="author_rule"
                                        required
                                        variant="outlined"
                                        :error-messages="
                                            form_errors.author
                                                ? form_errors.author[0]
                                                : ''
                                        "
                                        @update:modelValue="
                                            clearFieldError('author')
                                        "
                                        :class="
                                            form_errors.author ? 'mb-3' : ''
                                        "
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="form.pages"
                                        label="Nombre de pages *"
                                        :rules="pages_rule"
                                        required
                                        variant="outlined"
                                        :error-messages="
                                            form_errors.pages
                                                ? form_errors.pages[0]
                                                : ''
                                        "
                                        @update:modelValue="
                                            clearFieldError('pages')
                                        "
                                        :class="form_errors.pages ? 'mb-3' : ''"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.language"
                                        :items="languages"
                                        item-title="name"
                                        item-value="code"
                                        label="Langue *"
                                        :rules="language_rule"
                                        required
                                        variant="outlined"
                                        :error-messages="
                                            form_errors.language
                                                ? form_errors.language[0]
                                                : ''
                                        "
                                        @update:modelValue="
                                            clearFieldError('language')
                                        "
                                        :class="
                                            form_errors.language ? 'mb-3' : ''
                                        "
                                    ></v-select>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-select
                                        v-model="form.visibility"
                                        :items="visibilities"
                                        item-title="name"
                                        item-value="code"
                                        label="Visibilité *"
                                        :rules="visibility_rule"
                                        required
                                        variant="outlined"
                                        :error-messages="
                                            form_errors.visibility
                                                ? form_errors.visibility[0]
                                                : ''
                                        "
                                        @update:modelValue="
                                            clearFieldError('visibility')
                                        "
                                        :class="
                                            form_errors.visibility ? 'mb-3' : ''
                                        "
                                    ></v-select>
                                </v-col>

                                <v-col cols="12" md="12">
                                    <v-file-input
                                        v-model="form.file"
                                        label="Document *"
                                        @change="onFileChange"
                                        variant="outlined"
                                        accept=".pdf"
                                        prepend-icon="mdi-file-upload"
                                        show-size
                                        :error-messages="
                                            form_errors.file
                                                ? form_errors.file[0]
                                                : ''
                                        "
                                        @update:modelValue="
                                            clearFieldError('file')
                                        "
                                        :class="form_errors.file ? 'mb-3' : ''"
                                    ></v-file-input>
                                </v-col>

                                <v-col cols="12">
                                    <label class="text-subtitle-1 mb-2 d-block"
                                        >Tags</label
                                    >
                                    <div class="d-flex align-center mb-2">
                                        <v-text-field
                                            v-model="tag_input"
                                            label="Ajouter un tag"
                                            variant="outlined"
                                            hide-details
                                            density="compact"
                                            class="mr-2"
                                        ></v-text-field>
                                        <v-btn
                                            color="primary"
                                            icon
                                            @click="addTag"
                                        >
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </div>
                                    <v-chip-group
                                        v-if="form.tags.length"
                                        column
                                    >
                                        <v-chip
                                            v-for="(tag, index) in form.tags"
                                            :key="index"
                                            closable
                                            @click="removeTag(tag)"
                                            class="mr-2"
                                        >
                                            {{ tag }}
                                        </v-chip>
                                    </v-chip-group>
                                </v-col>
                            </v-row>

                            <v-divider class="my-4"></v-divider>

                            <v-row>
                                <v-col cols="12" class="d-flex justify-end">
                                    <v-btn
                                        color="secondary"
                                        variant="text"
                                        class="mr-2"
                                        :to="'/documents'"
                                    >
                                        Annuler
                                    </v-btn>
                                    <v-btn
                                        color="primary"
                                        :loading="doc_store.loading"
                                        :disabled="!form_valid"
                                        @click="updateDocument(doc?.id)"
                                    >
                                        <v-icon start>mdi-cloud-upload</v-icon>
                                        Modifier
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
