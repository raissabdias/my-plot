<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';

import AuthService from '../services/AuthService';

import Button from 'primevue/button';

import logoDark from '../assets/logo_black.png';
import logoLight from '../assets/logo_white.png';

const emit = defineEmits(['open-modal']);

const router = useRouter();
const user = ref({});
const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=0D8ABC&color=fff';
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
    loadUserFromStorage();
    window.addEventListener('user-updated', loadUserFromStorage);
});

onUnmounted(() => {
    window.removeEventListener('user-updated', loadUserFromStorage);
});

const closeMenu = () => {
    isMenuOpen.value = false;
};
</script>

<template>
    <nav class="bg-white dark:bg-gray-800 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <router-link to="/">
                        <img :src="logoDark" alt="My Plot Logo" class="h-14 w-auto block dark:hidden" />
                        <img :src="logoLight" alt="My Plot Logo" class="h-14 w-auto hidden dark:block" />
                    </router-link>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <router-link to="/library"
                        class="text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
                        Minha Estante
                    </router-link>
                    <div v-if="user.id" class="flex items-center gap-4 ml-4">
                        <router-link to="/profile" class="flex items-center gap-3 hover:opacity-80 transition">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
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
                    <div v-else>
                        <router-link to="/login"
                            class="text-indigo-600 font-medium hover:text-indigo-800">Entrar</router-link>
                    </div>
                </div>
                <div class="flex items-center md:hidden">
                    <button @click="isMenuOpen = !isMenuOpen" class="text-gray-600 dark:text-gray-200 hover:text-indigo-600 p-2 focus:outline-none">
                        <i :class="isMenuOpen ? 'pi pi-times' : 'pi pi-bars'" class="text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <div v-show="isMenuOpen" class="md:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <router-link to="/library" @click="closeMenu"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:text-indigo-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                    <i class="pi pi-book mr-2"></i> Minha Estante
                </router-link>
                <div v-if="user.id" class="border-t border-gray-100 dark:border-gray-700 mt-3 pt-3">
                    <router-link to="/profile" @click="closeMenu"
                        class="flex items-center px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <img :src="user.avatar || defaultAvatar" class="h-8 w-8 rounded-full mr-3 object-cover">
                        <span>Meu Perfil</span>
                    </router-link>
                    <button @click="handleLogout"
                        class="w-full text-left mt-1 block px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="pi pi-sign-out mr-2"></i> Sair
                    </button>
                </div>
                <div v-else class="mt-3 px-3">
                    <router-link to="/login" @click="closeMenu"
                        class="block text-center w-full bg-indigo-600 text-white px-4 py-2 rounded-md font-medium">
                        Entrar
                    </router-link>
                </div>
            </div>
        </div>
    </nav>
</template>