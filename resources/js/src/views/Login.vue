<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const email = ref("");
const password = ref("");
const show_password = ref(false);
const form_valid = ref(true);
const email_rules = [
    (value: string) => !!value || "L'email est requis",
    (value: string) => /.+@.+\..+/.test(value) || "L'email doit être valide",
];
const password_rules = [
    (value: string) => !!value || "Le mot de passe est requis",
];

async function login() {
    if (!form_valid.value) return;

    router.push("/documents");
}
</script>

<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12">
                    <v-toolbar color="primary" dark flat>
                        <v-toolbar-title>Connexion</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-alert type="error" variant="tonal" class="mb-4">
                            Erreur validation
                        </v-alert>

                        <v-form v-model="form_valid" @submit.prevent="login">
                            <v-text-field
                                v-model="email"
                                :rules="email_rules"
                                label="Email"
                                prepend-inner-icon="mdi-email"
                                variant="outlined"
                                required
                            ></v-text-field>

                            <v-text-field
                                v-model="password"
                                :rules="password_rules"
                                :type="show_password ? 'text' : 'password'"
                                label="Mot de passe"
                                prepend-inner-icon="mdi-lock"
                                variant="outlined"
                                required
                                :append-inner-icon="
                                    show_password ? 'mdi-eye-off' : 'mdi-eye'
                                "
                                @click:append-inner="
                                    show_password = !show_password
                                "
                            ></v-text-field>
                        </v-form>

                        <div class="d-flex justify-end">
                            <router-link
                                to="/register"
                                class="text-decoration-none"
                            >
                                Pas encore de compte ? S'inscrire
                            </router-link>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :disabled="!form_valid"
                            @click="login"
                        >
                            Se connecter
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
