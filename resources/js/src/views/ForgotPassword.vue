<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "@/store/auth";
import toast from "@/plugins/toast";

const router = useRouter();
const auth_store = useAuthStore();

const form = ref({
    email: "",
});
const form_valid = ref(true);
const email_rules = [
    (value: string) => !!value || "L'email est requis",
    (value: string) => /.+@.+\..+/.test(value) || "L'email doit être valide",
];

const form_errors = computed(() => auth_store.errors?.errors || {});

const clearFieldError = (field: string) => {
    if (form_errors.value[field]) {
        delete auth_store.errors.errors[field];
    }
};

watch(
    form,
    (new_form) => {
        Object.keys(new_form).forEach((field) => {
            if (form_errors.value[field]) {
                delete auth_store.errors.errors[field];
            }
        });
    },
    { deep: true }
);

async function forgotPassword() {
    if (!form_valid.value) return;

    const success = await auth_store.forgotPassword({
        email: form.value.email,
    });

    if (auth_store.errors) {
        console.log(auth_store.errors.errors);
        if (
            auth_store.errors.status == "419" ||
            auth_store.errors.status == "none"
        ) {
            toast(auth_store.errors.message, "error");
        }
    }

    if (success) {
        toast("Un code a été envoyé à votre adresse email", "success");
        router.push("/password-reset");
    }
}
</script>

<template>
    <v-container class="fill-height" fluid>
        <v-row align="center" justify="center">
            <v-col cols="12" sm="8" md="6" lg="4">
                <v-card class="elevation-12">
                    <v-toolbar color="primary" dark flat>
                        <v-toolbar-title>Mot de passe oublié</v-toolbar-title>
                    </v-toolbar>
                    <v-card-text>
                        <v-form
                            v-model="form_valid"
                            @submit.prevent="forgotPassword"
                        >
                            <v-text-field
                                v-model="form.email"
                                :rules="email_rules"
                                label="Email"
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
                            @click="forgotPassword"
                        >
                            Réinitialiser
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>
