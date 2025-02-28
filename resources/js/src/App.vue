<script setup lang="ts">
import { ref } from "vue";

const drawer = ref(false);
</script>

<template>
    <v-app>
        <v-app-bar color="primary" app>
            <v-app-bar-nav-icon
                class="d-md-none"
                @click="drawer = !drawer"
            ></v-app-bar-nav-icon>

            <v-app-bar-title class="d-none d-md-flex">
                <router-link to="/" class="text-decoration-none text-white">
                    DocShare
                </router-link>
            </v-app-bar-title>

            <template class="d-none d-md-flex">
                <v-btn to="/" variant="text">
                    <v-icon start>mdi-home</v-icon>
                    Accueil
                </v-btn>

                <!-- <template> -->
                <v-btn to="/documents" variant="text">
                    <v-icon start>mdi-file-document-multiple</v-icon>
                    Documents
                </v-btn>
                <v-btn to="/upload" variant="text">
                    <v-icon start>mdi-upload</v-icon>
                    Téléverser
                </v-btn>
                <!-- </template> -->
            </template>

            <v-spacer></v-spacer>

            <template>
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn icon v-bind="props">
                            <v-avatar color="primary" size="32">
                                <span class="text-subtitle-2 text-white">
                                    RA
                                </span>
                            </v-avatar>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item
                            :prepend-icon="'mdi-account'"
                            title="Mon profil"
                            to="/profile"
                        ></v-list-item>
                        <v-divider></v-divider>
                        <v-list-item
                            :prepend-icon="'mdi-logout'"
                            title="Se déconnecter"
                            @click="void"
                        ></v-list-item>
                    </v-list>
                </v-menu>
            </template>

            <!-- <template> -->
            <v-btn to="/login" variant="text">
                <v-icon start>mdi-login</v-icon>
                Connexion
            </v-btn>
            <!-- </template> -->
        </v-app-bar>

        <v-navigation-drawer v-model="drawer" temporary>
            <v-list>
                <v-list-item
                    to="/"
                    prepend-icon="mdi-home"
                    title="Accueil"
                ></v-list-item>

                <!-- <template> -->
                <v-list-item
                    to="/documents"
                    prepend-icon="mdi-file-document-multiple"
                    title="Documents"
                ></v-list-item>
                <v-list-item
                    to="/upload"
                    prepend-icon="mdi-upload"
                    title="Téléverser"
                ></v-list-item>
                <!-- </template> -->

                <v-divider></v-divider>

                <!-- <template> -->
                <v-list-item
                    to="/profile"
                    prepend-icon="mdi-account"
                    title="Mon profil"
                ></v-list-item>
                <v-list-item
                    prepend-icon="mdi-logout"
                    title="Se déconnecter"
                    @click="void"
                ></v-list-item>
                <!-- </template> -->
                <!-- <template> -->
                <v-list-item
                    to="/login"
                    prepend-icon="mdi-login"
                    title="Connexion"
                ></v-list-item>
                <!-- </template> -->
            </v-list>
        </v-navigation-drawer>

        <v-main>
            <router-view />
        </v-main>

        <v-footer app class="bg-grey-lighten-3">
            <v-row justify="center" no-gutters>
                <v-col class="text-center" cols="12">
                    <span
                        >&copy; {{ new Date().getFullYear() }} - DocShare</span
                    >
                </v-col>
            </v-row>
        </v-footer>
    </v-app>
</template>
