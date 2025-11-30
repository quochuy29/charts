import { ref } from 'vue';

const toasts = ref([]);

export function useToast() {
    const toast = ({ title, description, variant = 'default' }) => {
        const id = Date.now();
        toasts.value.push({ id, title, description, variant });

        // Auto remove after 3 seconds
        setTimeout(() => {
            dismiss(id);
        }, 3000);
    };

    const dismiss = (id) => {
        toasts.value = toasts.value.filter(t => t.id !== id);
    };

    return { toast, dismiss, toasts };
}