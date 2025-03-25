import {beforeEach, expect, it, vi} from "vitest";
import {mount, VueWrapper} from "@vue/test-utils";
import {createPinia, setActivePinia} from "pinia";
import {createVuetify} from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";

import DocumentCard from "../../components/DocumentCard.vue";
import {useAuthStore} from "@/store/auth.ts";
import {useDocumentStore} from "@/store/documents.ts";

vi.mock("@/helpers", () => ({
    clearFieldErrors: () => ({
        form_errors: {},
        clearFieldError: vi.fn()
    }),
    convertSize: vi.fn().mockReturnValue("100 KB"),
    formatDate: vi.fn().mockReturnValue("01/01/2024")
}));

vi.mock("@/store/auth", () => ({
    useAuthStore: vi.fn()
}));

vi.mock("@/store/documents", () => ({
    useDocumentStore: vi.fn()
}));

vi.mock("@/plugins/toast", () => ({
    default: vi.fn()
}));


describe("DocumentCard.vue", () => {
    let wrapper: VueWrapper<typeof DocumentCard>;
    let mockAuthStore: any;
    let mockDocumentStore: any;

    const mockDoc = {
        id: 1,
        user_id: 1,
        title: "Test Document",
        author: "John Doe",
        created_at: "2024-01-01T00:00:00Z",
        downloads: 10,
        size: 102400,
        tags: [
            {id: 1, name: "Tag1"},
            {id: 2, name: "Tag2"}
        ]
    };

    beforeEach(() => {
        const pinia = createPinia();
        setActivePinia(pinia);
        createVuetify({
            components,
            directives
        });

        mockAuthStore = {
            user: {
                id: "1",
                name: "John Doe",
                email: "john.doe@example.com",
                token: "xxx",
                created_at: "2024-01-01T00:00:00Z",
            }
        };

        mockDocumentStore = {
            loading2: false,
            shareDocument: vi.fn().mockResolvedValue(true),
            generateDocLink: vi.fn().mockResolvedValue("test-token"),
            downloadDocument: vi.fn(),
            errors: null
        };

        vi.mocked(useAuthStore).mockReturnValue(mockAuthStore);
        vi.mocked(useDocumentStore).mockReturnValue(mockDocumentStore);

        wrapper = mount(DocumentCard, {
            props: {
                doc: mockDoc
            }
        }) as unknown as VueWrapper<typeof DocumentCard>;
    });

    it("renders document details correctly", () => {
        expect(wrapper.text()).toContain(mockDoc.title);
        expect(wrapper.text()).toContain(mockDoc.author);
        expect(wrapper.text()).toContain("10");
        expect(wrapper.findAllComponents({name: "v-chip"}).length).toBe(mockDoc.tags.length);
    });

    it("calls download method when download button is clicked", async () => {
        const downloadButton = wrapper.findAllComponents({name: "v-btn"})
            .find(btn => btn.text().includes("Télécharger"));
        await downloadButton!.trigger("click");
        expect(mockDocumentStore.downloadDocument).toHaveBeenCalledWith(mockDoc.id);
    });

    it("copies share link to clipboard", async () => {
        const mockClipboard = {
            writeText: vi.fn()
        };

        Object.defineProperty(navigator, "clipboard", {
            value: mockClipboard,
            configurable: true
        });

        wrapper.vm.share_link = "http://test-link";
        await wrapper.vm.copyToClipboard();
        expect(mockClipboard.writeText).toHaveBeenCalledWith("http://test-link");
        expect(wrapper.vm.copied).toBe(true);
    });
});
