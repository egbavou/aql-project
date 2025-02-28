<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const name = ref("");
const email = ref("");
const password = ref("");
const confirm_password = ref("");
const show_password = ref(false);
const show_confirm_pass = ref(false);
const form_valid = ref(true);
const error = ref("");

const name_rules = [
    (value: string) => !!value || "Le nom est requis",
    (value: string) =>
        value.length >= 2 || "Le nom doit contenir au moins 2 caractères",
];

const email_rules = [
    (value: string) => !!value || "L'email est requis",
    (value: string) => /.+@.+\..+/.test(value) || "L'email doit être valide",
];

const password_rules = [
    (value: string) => !!value || "Le mot de passe est requis",
    (value: string) =>
        value.length >= 6 ||
        "Le mot de passe doit contenir au moins 6 caractères",
];

const confirm_pass_rules = [
    (value: string) => !!value || "La confirmation du mot de passe est requise",
    (value: string) =>
        value === password.value || "Les mots de passe ne correspondent pas",
];

async function register() {
    if (!form_valid.value) return;

    if (password.value !== confirm_password.value) {
        error.value = "Les mots de passe ne correspondent pas";
        return;
    }

    router.push("/documents");
}
</script>

<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12">
                    <v-toolbar color="primary" dark flat>
                        <v-toolbar-title>Inscription</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-alert
                            v-if="error != ''"
                            type="error"
                            variant="tonal"
                            class="mb-4"
                        >
                            {{ error }}
                        </v-alert>

                        <v-form v-model="form_valid" @submit.prevent="register">
                            <v-text-field
                                v-model="name"
                                :rules="name_rules"
                                label="Nom complet"
                                prepend-inner-icon="mdi-account"
                                variant="outlined"
                                required
                            ></v-text-field>

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

                            <v-text-field
                                v-model="confirmPassword"
                                :rules="confirm_pass_rules"
                                :type="show_confirm_pass ? 'text' : 'password'"
                                label="Confirmer le mot de passe"
                                prepend-inner-icon="mdi-lock-check"
                                variant="outlined"
                                required
                                :append-inner-icon="
                                    show_confirm_pass
                                        ? 'mdi-eye-off'
                                        : 'mdi-eye'
                                "
                                @click:append-inner="
                                    show_confirm_pass = !show_confirm_pass
                                "
                            ></v-text-field>
                        </v-form>

                        <div class="d-flex justify-end">
                            <router-link
                                to="/login"
                                class="text-decoration-none"
                            >
                                Déjà un compte ? Se connecter
                            </router-link>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :disabled="!form_valid"
                            @click="register"
                        >
                            S'inscrire
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
