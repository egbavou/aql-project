import { expect } from 'vitest';
import matchers from '@testing-library/jest-dom/matchers';
import { createPinia } from 'pinia';
import { config } from '@vue/test-utils';
import { createVuetify } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';


const pinia = createPinia();

const vuetify = createVuetify({
    components,
    directives,
});

config.global.plugins = [
    pinia,
    vuetify
];

config.global.mocks = {
};
