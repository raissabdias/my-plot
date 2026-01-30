<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';

import AuthService from '../services/AuthService';
import BookService from '../services/BookService';

import logoDark from '../assets/logo_black.png';
import logoLight from '../assets/logo_white.png';

import AutoComplete from 'primevue/autocomplete';

const router = useRouter();
const user = ref({});
const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=0D8ABC&color=fff';
const isMenuOpen = ref(false);

const selectedBook = ref(null);
const filteredBooks = ref([]);
const loadingSearch = ref(false);

const searchBook = async (event) => {
    const query = event.query.trim();
    if (!query) {
        filteredBooks.value = [];
        return;
    }

    loadingSearch.value = true;
    try {
        const response = await BookService.searchGoogle(query);
        filteredBooks.value = response.data;
    } catch (error) {
        console.error("Erro na busca:", error);
        filteredBooks.value = [];
    } finally {
        loadingSearch.value = false;
    }
};

const onBookSelect = (event) => {
    const book = event.value;
    router.push(`/book/${book.id}`);

    selectedBook.value = null;
    filteredBooks.value = [];
    isMenuOpen.value = false;
};

const getCleanCoverUrl = (url) => {
    if (!url) return null;
    return url.replace('http://', 'https://').replace('&edge=curl', '');
};

const handleLogout = async () => {
    await AuthService.logout();
    router.push('/login');
};

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
    <nav class="bg-white dark:bg-gray-800 shadow-sm transition-colors duration-300 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 gap-4">
                <div class="flex items-center shrink-0">
                    <router-link to="/">
                        <img :src="logoDark" alt="My Plot Logo" class="h-10 md:h-14 w-auto block dark:hidden" />
                        <img :src="logoLight" alt="My Plot Logo" class="h-10 md:h-14 w-auto hidden dark:block" />
                    </router-link>
                </div>
                <div class="hidden md:flex flex-1 items-center justify-center max-w-lg mx-auto">
                    <div class="w-full relative">
                        <span class="p-input-icon-left w-full">
                            <AutoComplete v-model="selectedBook" :suggestions="filteredBooks" @complete="searchBook"
                                @item-select="onBookSelect" optionLabel="title" placeholder="Buscar livros..."
                                class="w-full"
                                :inputClass="'w-full !pl-10 !bg-gray-100 dark:!bg-gray-700 !border-none focus:!ring-indigo-500 text-sm !rounded-full'"
                                :delay="500" :minLength="3" emptyMessage="Nenhum livro encontrado.">
                                <template #option="slotProps">
                                    <div class="flex items-center gap-3 p-1">
                                        <div class="shrink-0 w-8 h-12 bg-gray-200 rounded overflow-hidden">
                                            <img v-if="slotProps.option.image_url"
                                                :src="getCleanCoverUrl(slotProps.option.image_url)"
                                                class="w-full h-full object-cover">
                                            <div v-else
                                                class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="pi pi-image text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="flex flex-col overflow-hidden max-w-[250px]">
                                            <span class="font-medium text-gray-800 dark:text-white truncate text-sm">
                                                {{ slotProps.option.title }}
                                            </span>
                                            <span class="text-xs text-gray-500 truncate">
                                                {{ slotProps.option.author }}
                                            </span>
                                        </div>
                                    </div>
                                </template>
                            </AutoComplete>
                        </span>
                    </div>
                </div>
                <div class="hidden md:flex items-center space-x-6 shrink-0">
                    <router-link v-if="user.id" to="/library"
                        class="text-gray-600 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">
                        Minha Estante
                    </router-link>
                    <div v-if="user.id" class="flex items-center gap-4 ml-2">
                        <router-link to="/profile" class="flex items-center gap-3 hover:opacity-80 transition">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 max-w-[100px] truncate">
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
                        <router-link to="/login" class="text-indigo-600 font-medium hover:text-indigo-800">Entrar</router-link>
                        <span  class="text-neutral-600"> | </span>
                        <router-link to="/register" class="text-emerald-600 font-medium hover:text-indigo-800">Cadastrar</router-link>
                    </div>
                </div>
                <div class="flex items-center md:hidden">
                    <button @click="isMenuOpen = !isMenuOpen"
                        class="text-gray-600 dark:text-gray-200 hover:text-indigo-600 p-2 focus:outline-none">
                        <i :class="isMenuOpen ? 'pi pi-times' : 'pi pi-bars'" class="text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <div v-show="isMenuOpen"
            class="md:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 shadow-lg absolute w-full left-0 z-40">
            <div class="px-4 pt-4 pb-4 space-y-4">
                <div class="w-full relative">
                    <span class="p-input-icon-left w-full">
                        <AutoComplete v-model="selectedBook" :suggestions="filteredBooks" @complete="searchBook"
                            @item-select="onBookSelect" optionLabel="title" placeholder="Buscar livros..."
                            class="w-full"
                            :inputClass="'w-full !pl-10 !bg-gray-100 dark:!bg-gray-700 !border-none focus:!ring-indigo-500 text-sm !rounded-full'"
                            :delay="500" :minLength="3" emptyMessage="Nenhum livro encontrado.">
                            <template #option="slotProps">
                                <div class="flex items-center gap-3 p-1">
                                    <div class="shrink-0 w-8 h-12 bg-gray-200 rounded overflow-hidden">
                                        <img v-if="slotProps.option.image_url"
                                            :src="getCleanCoverUrl(slotProps.option.image_url)"
                                            class="w-full h-full object-cover">
                                        <div v-else
                                            class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="pi pi-image text-xs"></i>
                                        </div>
                                    </div>
                                    <div class="flex flex-col overflow-hidden max-w-[250px]">
                                        <span class="font-medium text-gray-800 dark:text-white truncate text-sm">
                                            {{ slotProps.option.title }}
                                        </span>
                                        <span class="text-xs text-gray-500 truncate">
                                            {{ slotProps.option.author }}
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </AutoComplete>
                    </span>
                </div>
                <router-link v-if="user.id" to="/library" @click="closeMenu"
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