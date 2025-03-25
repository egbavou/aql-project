import {mount, VueWrapper} from "@vue/test-utils";
import {createPinia, setActivePinia} from "pinia";
import {createVuetify} from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
import {beforeEach, describe, expect, it, vi} from "vitest";

import Login from "@/views/Login.vue";
import {useAuthStore} from "@/store/auth";

vi.mock("vue-router", () => ({
    useRouter: () => ({
        push: vi.fn()
    })
}));

vi.mock("@/store/auth", () => ({
    useAuthStore: vi.fn()
}));

vi.mock("@/plugins/toast", () => ({
    default: vi.fn()
}));

describe("Login.vue", () => {
    let wrapper: VueWrapper<typeof Login>;
    let mockAuthStore: any;
    let mockRouter: any;

    beforeEach(() => {
        const pinia = createPinia();
        setActivePinia(pinia);
        createVuetify({
            components,
            directives
        });

        mockAuthStore = {
            login: vi.fn(),
            loading: false,
            errors: null
        };

        mockRouter = {
            push: vi.fn()
        };

        vi.mocked(useAuthStore).mockReturnValue(mockAuthStore);

        wrapper = mount(Login, {
            global: {
                mocks: {
                    $router: mockRouter
                }
            }
        }) as unknown as VueWrapper<typeof Login>;
    });

    it("renders login form correctly", () => {
        expect(wrapper.text()).toContain("Connexion");
        expect(wrapper.find("input[type=\"text\"]").exists()).toBe(true);
        expect(wrapper.find("input[type=\"password\"]").exists()).toBe(true);
    });

    it("navigates to registration page", () => {
        const registerLink = wrapper.findAll("router-link").find(a => a.text().includes("S'inscrire"));
        expect(registerLink!.attributes("to")).toBe("/register");
    });

    it("navigates to forgot password page", () => {
        const forgotPasswordLink = wrapper.findAll("router-link").find(a => a.text().includes("Mot de passe oublié ?"));
        expect(forgotPasswordLink!.attributes("to")).toBe("/forgot-password");
    });
});
