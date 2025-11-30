import { ref, onMounted, onUnmounted } from 'vue';

const MOBILE_BREAKPOINT = 768;

export function useIsMobile() {
    const isMobile = ref(false);

    const onChange = () => {
        isMobile.value = window.innerWidth < MOBILE_BREAKPOINT;
    };

    onMounted(() => {
        const mql = window.matchMedia(`(max-width: ${MOBILE_BREAKPOINT - 1}px)`);
        mql.addEventListener("change", onChange);
        isMobile.value = window.innerWidth < MOBILE_BREAKPOINT;

        onUnmounted(() => {
            mql.removeEventListener("change", onChange);
        });
    });

    return isMobile;
}