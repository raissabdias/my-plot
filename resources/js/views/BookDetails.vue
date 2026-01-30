<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';

import BookService from '../services/BookService';
import TheNavbar from '../components/TheNavbar.vue';

import Button from 'primevue/button';
import Rating from 'primevue/rating';
import Tag from 'primevue/tag';
import Skeleton from 'primevue/skeleton';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const book = ref(null);
const loading = ref(true);
const isLoading = ref(false);
const user = ref({});

const fetchBookDetails = async (id) => {
    loading.value = true;
    try {
        const response = await BookService.getBookDetails(id);
        book.value = response.data;
    } catch (error) {
        console.error("Error fetching book:", error);
    } finally {
        loading.value = false;
    }
};

const goBack = () => {
    router.back();
};

// Função para limpar a URL da imagem (reutilizada)
const getCleanCoverUrl = (url) => {
    if (!url) return null;
    let cleanUrl = url.replace('http://', 'https://');
    cleanUrl = cleanUrl.replace('&edge=curl', '');
    return cleanUrl;
};

// Formatar data
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('pt-BR', { year: 'numeric', month: 'long', day: 'numeric' });
};

const getStatusColor = (color) => {
    switch (color) {
        case 'green':
            return '!bg-emerald-600 !text-white border-emerald-500';
        case 'blue':
            return '!bg-blue-600 !text-white border-blue-500';
        case 'gray':
            return '!bg-gray-700 !text-white border-gray-600';
        default:
            return '!bg-gray-500 !text-white';
    }
};

onMounted(() => {
    const bookId = route.params.id;
    if (bookId) {
        fetchBookDetails(bookId);
    }

    const userData = localStorage.getItem('user_data');
    if (userData) {
        user.value = JSON.parse(userData);
    }
});

watch(() => route.params.id, (newId) => {
    if (newId) {
        fetchBookDetails(newId);
    }
});

const addToBookshelf = async () => {
    const payload = {
        google_book_id: book.value.id,
        title: book.value.title,
        authors: book.value.authors,
        isbn: book.value.isbn,
        image_url: book.value.image_url,
        page_count: book.value.page_count
    };

    try {
        isLoading.value = true;

        const response = await BookService.addToShelf(payload);
        book.value.user_status = response.data.book.status_formatted;

        toast.add({ severity: 'success', summary: 'Adicionado', detail: 'Livro adicionado à estante!', life: 3000 });
    } catch (error) {
        console.error("Erro ao salvar:", error);
        const errorMsg = error.response?.data?.message || 'Não foi possível salvar o livro.';
        toast.add({ severity: 'error', summary: 'Erro', detail: errorMsg, life: 3000 });
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <TheNavbar />
        <main class="max-w-5xl mx-auto p-4 md:p-8">
            <div v-if="loading"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 md:p-10">
                    <div class="flex flex-col md:flex-row gap-8 md:gap-10 items-start">
                        <div class="w-full md:w-auto flex justify-center flex-shrink-0">
                            <Skeleton width="180px" height="270px" borderRadius="12px" />
                        </div>
                        <div class="flex-1 w-full">
                            <div class="flex justify-between items-start gap-4 mb-4">
                                <Skeleton width="60%" height="2.5rem" />
                                <Skeleton width="4rem" height="2rem" borderRadius="6px" />
                            </div>
                            <Skeleton width="40%" height="1.5rem" class="mb-4" />
                            <div class="flex gap-2 mb-6">
                                <Skeleton width="4rem" height="1.5rem" borderRadius="2rem" />
                                <Skeleton width="5rem" height="1.5rem" borderRadius="2rem" />
                                <Skeleton width="3rem" height="1.5rem" borderRadius="2rem" />
                            </div>

                            <Skeleton width="30%" height="2rem" class="mb-6" />
                            <Skeleton width="20%" height="1rem" />
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/50 p-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <Skeleton width="80%" height="1rem" v-for="i in 4" :key="i" />
                    </div>
                </div>
            </div>
            <div v-else-if="book"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 md:p-10">
                    <div class="flex flex-col md:flex-row gap-8 md:gap-10 items-start">
                        <div class="w-full md:w-auto flex justify-center flex-shrink-0">
                            <div class="relative w-44 shadow-2xl rounded-lg overflow-hidden group aspect-[2/3]">
                                <img v-if="book.image_url" :src="getCleanCoverUrl(book.image_url)" :alt="book.title"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                <div v-else
                                    class="w-full h-full bg-gray-200 dark:bg-gray-700 flex flex-col items-center justify-center text-gray-400">
                                    <i class="pi pi-image text-3xl mb-2"></i>
                                    <span class="text-xs">Sem Capa</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 w-full text-center md:text-left">
                            <div
                                class="flex flex-col-reverse md:flex-row justify-between items-center md:items-start gap-4 mb-2">
                                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ book.title }}
                                </h1>
                                <Button label="Voltar" icon="pi pi-arrow-left" text @click="goBack"
                                    class="!px-3 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 flex-shrink-0" />
                            </div>
                            <p class="text-xl text-gray-600 dark:text-gray-300 mb-4">
                                de <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{
                                    book.authors?.join(', ') || 'Desconhecido' }}</span>
                            </p>
                            <div v-if="book.categories"
                                class="flex flex-wrap gap-2 justify-center md:justify-start mb-6">
                                <Tag v-for="cat in book.categories" :key="cat" :value="cat" severity="secondary"
                                    class="!bg-gray-100 dark:!bg-gray-700 !text-gray-500 dark:!text-gray-400 border border-gray-200 dark:border-gray-600 !font-normal"
                                    rounded />
                            </div>
                            <div v-if="user.id" class="mb-6 flex justify-center md:justify-start">
                                <div v-if="book.user_status">
                                    <Tag :value="book.user_status.label" :icon="book.user_status.icon"
                                        :class="getStatusColor(book.user_status.color)"
                                        class="!text-base !px-4 !py-2 !font-medium shadow-sm" rounded />
                                </div>
                                <div v-else>
                                    <Button label="Quero ler" icon="pi pi-bookmark" @click="addToBookshelf"
                                        severity="primary" raised :loading="isLoading" />
                                </div>
                            </div>
                            <div class="flex items-center justify-center md:justify-start gap-3"
                                v-if="book.average_rating">
                                <Rating :modelValue="book.average_rating" readonly :cancel="false" />
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    ({{ book.ratings_count || 0 }} avaliações)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gray-50 dark:bg-gray-700/30 border-y border-gray-100 dark:border-gray-700 px-6 md:px-10 py-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                        <div class="flex flex-col">
                            <span
                                class="text-gray-400 dark:text-gray-400 text-xs uppercase font-bold tracking-wider mb-1">Páginas</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ book.page_count || '-'
                            }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-gray-400 dark:text-gray-400 text-xs uppercase font-bold tracking-wider mb-1">Editora</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 truncate"
                                :title="book.publisher">{{ book.publisher || '-' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-gray-400 dark:text-gray-400 text-xs uppercase font-bold tracking-wider mb-1">Publicação</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{
                                formatDate(book.published_date) || '-' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-gray-400 dark:text-gray-400 text-xs uppercase font-bold tracking-wider mb-1">ISBN</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200 font-mono">{{ book.isbn || '-'
                            }}</span>
                        </div>
                    </div>
                </div>
                <div class="p-6 md:p-10">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="pi pi-align-left text-indigo-500"></i> Sinopse
                    </h3>
                    <div v-if="book.description"
                        class="text-gray-600 dark:text-gray-300 leading-relaxed text-lg description-content text-justify"
                        v-html="book.description">
                    </div>
                    <p v-else class="text-gray-400 italic">Nenhuma descrição disponível.</p>
                </div>
            </div>
            <div v-else class="text-center py-20">
                <i class="pi pi-exclamation-circle text-4xl text-gray-300 mb-4"></i>
                <h2 class="text-xl text-gray-600 dark:text-gray-400">Livro não encontrado.</h2>
                <Button label="Voltar para Biblioteca" link @click="router.push('/library')" />
            </div>

        </main>
    </div>
</template>