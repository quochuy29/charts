<template>
    <div class="flex h-screen w-full bg-gray-50">
        <Sidebar v-if="!isLoginPage" mode="dashboard" :selectedNode="selectedNode"
            @update:selectedNode="selectedNode = $event" />

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header v-if="!isLoginPage"
                class="h-16 flex items-center justify-between px-6 bg-white border-b border-gray-200 shrink-0 z-20 shadow-sm">
                <div class="flex items-center gap-3">
                    <div
                        class="flex items-center gap-4">
                        <button
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-7 w-7"
                            data-sidebar="trigger"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-panel-left">
                                <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                <path d="M9 3v18"></path>
                            </svg><span class="sr-only">Toggle Sidebar</span></button>
                    </div>
                    <div>
                        <div class="text-sm font-medium">[LOGO]</div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-gray-800">Welcome, {{ userRole }}</div>
                    </div>
                    <div
                        class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold border border-slate-200">
                        {{ userRole.charAt(0).toUpperCase() }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-auto bg-gray-50 relative">
                <router-view></router-view>
            </main>
        </div>
    </div>
</template>

<script setup>
    import { computed, ref } from 'vue';
    import { useRoute } from 'vue-router';
    import Sidebar from './components/layout/Sidebar.vue';

    const route = useRoute();
    const isLoginPage = computed(() => route.path === '/login');
    const userRole = localStorage.getItem('userRole') || 'Guest';

    const selectedNode = ref({
        id: "factory-1",
        name: "Factory 1 - Main Production",
        type: "factory",
    });
</script>
<style scoped>
    svg {
        width: 1rem;
    }
</style>