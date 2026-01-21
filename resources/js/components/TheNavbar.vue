<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

import AuthService from '../services/AuthService';

import Button from 'primevue/button';

import logoDark from '../assets/logo_black.png';
import logoLight from '../assets/logo_white.png';

const emit = defineEmits(['open-modal']);

const router = useRouter();
const route = useRoute();
const user = ref({});
const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=0D8ABC&color=fff';

// Menu dropdown (opcional, para mobile)
const isMenuOpen = ref(false);

const handleLogout = async () => {
    await AuthService.logout();
    router.push('/login');
};

// Dados do usuário a partir do localStorage
const loadUserFromStorage = () => {
    const userData = localStorage.getItem('user_data');
    if (userData) {
        user.value = JSON.parse(userData);
    }
};

onMounted(() => {
    const userData = localStorage.getItem('user_data');
    if (userData) {
        user.value = JSON.parse(userData);
    }
    window.addEventListener('user-updated', loadUserFromStorage);
});

onUnmounted(() => {
    window.removeEventListener('user-updated', loadUserFromStorage);
});
</script>

<template>
    <nav
        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <router-link to="/">
                        <img :src="logoDark" alt="My Plot Logo" class="h-14 w-auto block dark:hidden" />
                        <img :src="logoLight" alt="My Plot Logo" class="h-14 w-auto hidden dark:block" />
                    </router-link>
                </div>
                <div class="flex items-center gap-4">
                    <router-link to="/library" v-if="route.path !== '/library'">
                        <Button label="Minha Estante" icon="pi pi-book" severity="secondary" text
                            class="hidden md:flex" />
                    </router-link>
                    <div class="hidden md:block" v-if="route.path === '/library'">
                        <Button label="Novo Livro" icon="pi pi-plus" severity="primary" raised
                            @click="$emit('open-modal')" />
                    </div>
                    <div class="flex items-center gap-4">
                        <div v-if="user.id" class="flex items-center gap-4">
                            <router-link to="/profile" class="flex items-center gap-3 hover:opacity-80 transition">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-200 hidden md:block">
                                    {{ user.name }}
                                </span>
                                <img :src="user.avatar || defaultAvatar" alt="Avatar"
                                    class="w-9 h-9 rounded-full border-2 border-indigo-100 object-cover">
                            </router-link>
                            <button @click="handleLogout" class="text-gray-500 hover:text-red-500 transition ml-2"
                                title="Sair">
                                <i class="pi pi-sign-out text-xl"></i>
                            </button>
                        </div>
                        <div v-else class="flex gap-2">
                            <router-link to="/login"
                                class="text-gray-600 hover:text-indigo-600 font-medium">Entrar</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>