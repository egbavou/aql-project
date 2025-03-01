import { useToast } from "vue-toast-notification";
import "vue-toast-notification/dist/theme-sugar.css";

const $toast = useToast();

function toast(array: string[] | string, type: string) {
    $toast.open({
        message: !Array.isArray(array)
            ? array
            : "",
        type: type,
        position: "top-right",
        duration: 5000
    });
}

export default toast