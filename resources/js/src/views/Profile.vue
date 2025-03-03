<script setup lang="ts">
import { ref } from "vue";
import { useDocumentStore } from "../store/documents";
import { useAuthStore } from "@/store/auth";
import toast from "@/plugins/toast";
import { useRouter } from "vue-router";

const router = useRouter();
const auth_store = useAuthStore();
const documentStore = useDocumentStore();
const show_change_pass_dialog = ref(false);
const current_password = ref("");
const new_password = ref("");
const confirm_new_password = ref("");

const user_docs = documentStore.documents.slice(0, 2);

async function changePassword() {}

async function logout() {
    const success = await auth_store.logout();

    if (success) {
        toast("Vous êtes déconnecté à présent", "success");
        router.push("/");
    }
}
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
                            @click="show_change_pass_dialog = true"
                        >
                            <v-icon start>mdi-lock</v-icon>
                            Changer le mot de passe
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
                        <v-btn color="primary" variant="text" to="/upload">
                            <v-icon start>mdi-plus</v-icon>
                            Ajouter un document
                        </v-btn>
                    </v-card-title>

                    <v-card-text v-if="user_docs.length > 0">
                        <v-list>
                            <v-list-item
                                v-for="doc in user_docs"
                                :key="doc.id"
                                :to="`/document/${doc.id}`"
                            >
                                <v-list-item-title>{{
                                    doc.title
                                }}</v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ doc.upload_date }} • {{ doc.size }}
                                </v-list-item-subtitle>

                                <template v-slot:append>
                                    <v-btn
                                        icon
                                        variant="text"
                                        :href="doc.path"
                                        target="_blank"
                                    >
                                        <v-icon>mdi-download</v-icon>
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

        <v-dialog v-model="show_change_pass_dialog" max-width="500px">
            <v-card>
                <v-card-title>Changer le mot de passe</v-card-title>
                <v-card-text>
                    <v-alert type="error" variant="tonal" class="mb-4">
                        Erreur
                    </v-alert>

                    <v-alert type="success" variant="tonal" class="mb-4">
                        Mot de passe modifié avec succès
                    </v-alert>

                    <v-text-field
                        v-model="current_password"
                        label="Mot de passe actuel"
                        type="password"
                        variant="outlined"
                        class="mb-2"
                    ></v-text-field>

                    <v-text-field
                        v-model="new_password"
                        label="Nouveau mot de passe"
                        type="password"
                        variant="outlined"
                        class="mb-2"
                    ></v-text-field>

                    <v-text-field
                        v-model="confirm_new_password"
                        label="Confirmer le nouveau mot de passe"
                        type="password"
                        variant="outlined"
                    ></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="grey"
                        variant="text"
                        @click="show_change_pass_dialog = false"
                        >Annuler</v-btn
                    >
                    <v-btn color="primary" @click="changePassword"
                        >Modifier</v-btn
                    >
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>
