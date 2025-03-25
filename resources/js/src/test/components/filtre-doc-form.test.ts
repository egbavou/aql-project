import {mount, VueWrapper} from "@vue/test-utils";
import FiltreDocForm from "@/components/FiltreDocForm.vue";
import {useDocumentStore} from "@/store/documents.ts";
import {createPinia, setActivePinia} from "pinia";
import {vi} from "vitest";

describe("FiltreDocForm.vue", () => {
    let wrapper: VueWrapper<typeof FiltreDocForm>;

    const mockTags = [
        {
            id: 1,
            name: "Tag 1",
            created_at: "2024-01-01T00:00:00Z",
            updated_at: "2024-01-01T00:00:00Z"
        },
        {
            id: 2,
            name: "Tag 2",
            created_at: "2024-01-01T00:00:00Z",
            updated_at: "2024-01-01T00:00:00Z"
        },
    ];

    beforeEach(() => {
        const pinia = createPinia();
        setActivePinia(pinia);

        wrapper = mount(FiltreDocForm, {
            props: {
                search_title: "Test title",
                search_author: "Test author",
                selected_language: "en",
                selected_tag: 1,
            },
        }) as VueWrapper<typeof FiltreDocForm>;

        const docStore = useDocumentStore();
        docStore.tags = mockTags;
    });

    it("renders correctly", () => {
        expect(wrapper.findComponent({name: "v-text-field"}).exists()).toBe(true);
        expect(wrapper.findComponent({name: "v-select"}).exists()).toBe(true);

        expect(wrapper.findAllComponents({name: "v-text-field"}).at(0)!.props("modelValue")).toBe("Test title");
        expect(wrapper.findAllComponents({name: "v-text-field"}).at(1)!.props("modelValue")).toBe("Test author");
        expect(wrapper.findAllComponents({name: "v-select"}).at(0)!.props("modelValue")).toBe(1);
        expect(wrapper.findAllComponents({name: "v-select"}).at(1)!.props("modelValue")).toBe("en");
    });

    it("emits updates when input values change", async () => {
        const searchTitleField = wrapper.findComponent({name: "v-text-field"});
        const searchAuthorField = wrapper.findAllComponents({name: "v-text-field"}).at(1);
        const selectTagField = wrapper.findAllComponents({name: "v-select"}).at(0);
        const selectLanguageField = wrapper.findAllComponents({name: "v-select"}).at(1);

        await searchTitleField.setValue("New title");
        await searchAuthorField!.setValue("New author");
        await selectTagField!.setValue(2);
        await selectLanguageField!.setValue("fr");

        expect(wrapper.emitted()["update:search_title"][0]).toEqual(["New title"]);
        expect(wrapper.emitted()["update:search_author"][0]).toEqual(["New author"]);
        expect(wrapper.emitted()["update:selected_tag"][0]).toEqual([2]);
        expect(wrapper.emitted()["update:selected_language"][0]).toEqual(["fr"]);
    });

    it("fetches tags if they are empty", async () => {
        const docStore = useDocumentStore();
        const mockGetDocumentsTags = vi.fn();
        vi.spyOn(docStore, "getDocumentsTags").mockImplementation(mockGetDocumentsTags);
        docStore.tags = [];

        wrapper = mount(FiltreDocForm, {
            props: {
                search_title: "Test title",
                search_author: "Test author",
                selected_language: "en",
                selected_tag: 1,
            },
        }) as unknown as VueWrapper<typeof FiltreDocForm>;

        await wrapper.vm.$nextTick();
        expect(mockGetDocumentsTags).toHaveBeenCalled();
        vi.restoreAllMocks();
    });
});
