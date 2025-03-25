import {defineConfig} from "vitest/config";
import vue from "@vitejs/plugin-vue";
import {fileURLToPath} from "node:url";

export default defineConfig({
    plugins: [vue()],
    test: {
        globals: true,
        environment: "jsdom",
        setupFiles: "./vitest.setup.ts",
        server: {
            deps: {
                inline: ['vuetify'],
            },
        },
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': fileURLToPath(new URL('./resources/js/src', import.meta.url)),
        },
    }
});
