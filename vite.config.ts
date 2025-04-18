import { sentryVitePlugin } from "@sentry/vite-plugin";
import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url'
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    /* server: {
        host: '0.0.0.0'
    }, */
    plugins: [laravel({
        input: ['resources/css/app.css', 'resources/js/app.ts'],
        refresh: true,
    }), vue(), sentryVitePlugin({
        org: "aql-jz",
        project: "aql-frontend"
    })],

    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@': fileURLToPath(new URL('./resources/js/src', import.meta.url)),
        },
    },

    build: {
        sourcemap: true
    }
});
