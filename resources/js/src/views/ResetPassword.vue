<script setup lang="ts">
import {ref} from "vue";
import {useRouter} from "vue-router";
import {useAuthStore} from "@/store/auth";
import toast from "@/plugins/toast";
import {clearFieldErrors} from "@/helpers";

const router = useRouter();
const auth_store = useAuthStore();

const form = ref({
    token: "",
    password: "",
});
const confirm_password = ref("");
const show_password = ref(false);
const show_confirm_pass = ref(false);
const form_valid = ref(true);
const token_rule = [(value: string) => !!value || "Le code est requis"];
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

async function resetPassword() {
    if (!form_valid.value) return;

    const success = await auth_store.resetPassword({
        token: form.value.token,
        password: form.value.password,
    });

    if (auth_store.errors) {
        if (["419", "401", "none"].includes(auth_store.errors.status)) {
            toast(auth_store.errors.message, "error");
        }
    }

    if (success) {
        toast("Mot de passe réinitialisé. Connectez-vous à présent", "success");
        router.push("/login");
    }
}
</script>

<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12">
                    <v-toolbar color="primary" dark flat>
                        <v-toolbar-title
                            >Réinitialiser mot de passe</v-toolbar-title
                        >
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                            v-model="form_valid"
                            @submit.prevent="resetPassword"
                        >
                            <v-text-field
                                v-model="form.token"
                                :rules="token_rule"
                                label="Code"
                                prepend-inner-icon="mdi-email"
                                variant="outlined"
                                required
                                class="mb-3"
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
                                Se connecter
                            </router-link>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primary"
                            :loading="auth_store.loading"
                            :disabled="!form_valid"
                            @click="resetPassword"
                        >
                            Réinitialiser
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
