<script setup lang="ts">
import {useAuthStore} from "@/store/auth";
import {ref} from "vue";
import {useRouter} from "vue-router";
import toast from "@/plugins/toast";
import {clearFieldErrors} from "@/helpers";

const router = useRouter();
const auth_store = useAuthStore();

const form = ref({
    email: "",
    password: "",
    name: "",
});
const confirm_password = ref("");
const show_password = ref(false);
const show_confirm_pass = ref(false);
const form_valid = ref(true);

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
        value.length >= 8 ||
        "Le mot de passe doit contenir au moins 8 caractères",
];

const confirm_pass_rules = [
    (value: string) => !!value || "La confirmation du mot de passe est requise",
    (value: string) =>
        value === form.value.password ||
        "Les mots de passe ne correspondent pas",
];

const { form_errors, clearFieldError } = clearFieldErrors(auth_store, form);

async function register() {
    if (!form_valid.value) return;

    const success = await auth_store.register({
        email: form.value.email,
        name: form.value.name,
        password: form.value.password,
    });

    if (auth_store.errors) {
        console.log(auth_store.errors.errors);
        if (["419", "401", "none"].includes(auth_store.errors.status)) {
            toast(auth_store.errors.message, "error");
        }
    }

    if (success) {
        toast(
            "Inscription réussie ! Vous êtes à présent connecté !",
            "success"
        );
        router.push("/documents");
    }
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
                        <v-form v-model="form_valid" @submit.prevent="register">
                            <v-text-field
                                v-model="form.name"
                                :rules="name_rules"
                                label="Nom complet"
                                class="mb-3"
                                prepend-inner-icon="mdi-account"
                                variant="outlined"
                                required
                                :error-messages="
                                    form_errors.name ? form_errors.name[0] : ''
                                "
                                @update:modelValue="clearFieldError('name')"
                            ></v-text-field>

                            <v-text-field
                                v-model="form.email"
                                :rules="email_rules"
                                label="Email"
                                class="mb-3"
                                prepend-inner-icon="mdi-email"
                                variant="outlined"
                                required
                                :error-messages="
                                    form_errors.email
                                        ? form_errors.email[0]
                                        : ''
                                "
                                @update:modelValue="clearFieldError('email')"
                            ></v-text-field>

                            <v-text-field
                                v-model="form.password"
                                :rules="password_rules"
                                :type="show_password ? 'text' : 'password'"
                                label="Mot de passe"
                                class="mb-3"
                                prepend-inner-icon="mdi-lock"
                                variant="outlined"
                                required
                                :append-inner-icon="
                                    show_password ? 'mdi-eye-off' : 'mdi-eye'
                                "
                                @click:append-inner="
                                    show_password = !show_password
                                "
                                :error-messages="
                                    form_errors.password
                                        ? form_errors.password[0]
                                        : ''
                                "
                                @update:modelValue="clearFieldError('password')"
                            ></v-text-field>

                            <v-text-field
                                v-model="confirm_password"
                                :rules="confirm_pass_rules"
                                :type="show_confirm_pass ? 'text' : 'password'"
                                label="Confirmer le mot de passe"
                                class="mb-3"
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
                                style="color: blue"
                            >
                                Déjà un compte ? Se connecter
                            </router-link>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :loading="auth_store.loading"
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
