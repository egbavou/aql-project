<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const title = ref("");
const description = ref("");
const author = ref("");
const tag_input = ref("");
const tags = ref<string[]>([]);
const file = ref<File | null>(null);
const loading = ref(false);
</script>

<template>
    <v-container>
        <v-row justify="center">
            <v-col cols="12" md="8">
                <h1 class="text-h4 mb-6">Téléverser un document</h1>

                <v-card>
                    <v-card-text>
                        <v-form>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        v-model="title"
                                        label="Titre du document *"
                                        required
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12">
                                    <v-textarea
                                        v-model="description"
                                        label="Description *"
                                        required
                                        variant="outlined"
                                        rows="3"
                                    ></v-textarea>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-text-field
                                        v-model="author"
                                        label="Auteur *"
                                        required
                                        variant="outlined"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-file-input
                                        v-model="file"
                                        label="Fichier *"
                                        required
                                        variant="outlined"
                                        accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt"
                                        prepend-icon="mdi-file-upload"
                                        show-size
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
                                        <v-btn color="primary" icon>
                                            <v-icon>mdi-plus</v-icon>
                                        </v-btn>
                                    </div>
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
                                    <v-btn color="primary" type="submit">
                                        <v-icon start>mdi-cloud-upload</v-icon>
                                        Téléverser
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
