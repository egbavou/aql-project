<script setup lang="ts">
import { computed, onMounted, ref, watch } from "vue";
import { useDocumentStore } from "../store/documents";
import { useAuthStore } from "@/store/auth";
import toast from "@/plugins/toast";
import { useRouter } from "vue-router";
import { formatDate, convertSize, clearFieldErrors } from "@/helpers";

const router = useRouter();
const auth_store = useAuthStore();
const doc_store = useDocumentStore();
const show_edit_profil_dialog = ref(false);
const form_valid = ref(true);

const form = ref({
    name: auth_store.user?.name,
    email: auth_store.user?.email,
});

const { form_errors, clearFieldError } = clearFieldErrors(auth_store, form);

async function updateProfile() {
    if (!form_valid.value) return;

    const success = await auth_store.udpateProfile(
        Number(auth_store.user?.id),
        {
            name: form.value.name,
            email: form.value.email,
        }
    );

    if (success) {
        toast("Informations profil modifié", "success");
        router.push("/profile");
    }
}

async function logout() {
    const success = await auth_store.logout();

    if (success) {
        toast("Vous êtes déconnecté à présent", "success");
        router.push("/");
    }
}

onMounted(async () => {
    await doc_store.getDocumentsMe("created");
});

const my_docs = computed(() => doc_store.documents.slice(0, 5));
</script>

<template>
    <v-container>
        <h1 class="text-h4 mb-6">Mon profil</h1>

        <v-row>
            <v-col cols="12" md="4">
                <v-card class="mb-4">
                    <v-card-text class="text-center">
                        <v-avatar size="120" color="primary" class="mb-4">
                            <span class="text-h4 text-white">{{
                                auth_store.user?.name.substr(0, 2)
                            }}</span>
                        </v-avatar>
                        <h2 class="text-h5">{{ auth_store.user?.name }}</h2>
                        <p class="text-subtitle-1 text-medium-emphasis">
                            {{ auth_store.user?.email }}
                        </p>
                        <v-chip class="mt-2" color="primary" size="small">
                            En ligne
                        </v-chip>
                    </v-card-text>
                </v-card>

                <v-card>
                    <v-card-title>Actions</v-card-title>
                    <v-card-text>
                        <v-btn
                            block
                            color="primary"
                            class="mb-3"
                            @click="show_edit_profil_dialog = true"
                        >
                            <v-icon start>mdi-account</v-icon>
                            Modifier profil
                        </v-btn>

                        <v-btn
                            block
                            color="error"
                            variant="outlined"
                            :loading="auth_store.loading"
                            @click="logout"
                        >
                            <v-icon start>mdi-logout</v-icon>
                            Se déconnecter
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>

            <v-col cols="12" md="8">
                <v-card>
                    <v-card-title
                        class="d-flex justify-space-between align-center"
                    >
                        <span>Mes documents</span>
                        <v-btn
                            color="primary"
                            variant="text"
                            to="/document/add"
                        >
                            <v-icon start>mdi-plus</v-icon>
                            Ajouter un document
                        </v-btn>
                    </v-card-title>

                    <v-card-text v-if="my_docs">
                        <v-list>
                            <v-list-item
                                v-for="doc in my_docs"
                                :key="doc.id"
                                :to="`/document/${doc.id}`"
                            >
                                <v-list-item-title>{{
                                    doc.title
                                }}</v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ formatDate(doc.created_at) }} •
                                    {{ convertSize(doc.size) }}
                                </v-list-item-subtitle>

                                <template v-slot:append>
                                    <v-btn
                                        icon
                                        variant="text"
                                        :to="`/document/${doc.id}`"
                                        target="_blank"
                                    >
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                </template>
                            </v-list-item>
                        </v-list>
                    </v-card-text>

                    <v-card-text v-else>
                        <v-alert type="info" variant="tonal">
                            Vous n'avez pas encore téléversé de documents.
                        </v-alert>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="show_edit_profil_dialog" max-width="500px">
            <v-form v-model="form_valid" @submit.prevent="updateProfile">
                <v-card>
                    <v-card-title>Modifier vos informations</v-card-title>
                    <v-card-text>
                        <v-text-field
                            v-model="form.name"
                            label="Nom complet"
                            type="text"
                            variant="outlined"
                            class="mb-2"
                            :error-messages="
                                form_errors.email ? form_errors.name[0] : ''
                            "
                            @update:modelValue="clearFieldError('name')"
                        ></v-text-field>

                        <v-text-field
                            v-model="form.email"
                            label="Email"
                            type="email"
                            variant="outlined"
                            class="mb-2"
                            :error-messages="
                                form_errors.email ? form_errors.email[0] : ''
                            "
                            @update:modelValue="clearFieldError('email')"
                        ></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="grey"
                            variant="text"
                            @click="show_edit_profil_dialog = false"
                            >Annuler</v-btn
                        >
                        <v-btn
                            color="primary"
                            :loading="auth_store.loading"
                            @click="updateProfile"
                            >Modifier</v-btn
                        >
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </v-container>
</template>
