<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import BookService from '../services/BookService';
import TheNavbar from '../components/TheNavbar.vue';

import Button from 'primevue/button';
import Rating from 'primevue/rating';
import Tag from 'primevue/tag';
import Skeleton from 'primevue/skeleton';
import Divider from 'primevue/divider';

const route = useRoute();
const router = useRouter();

const book = ref(null);
const loading = ref(true);

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

onMounted(() => {
    const bookId = route.params.id;
    if (bookId) {
        fetchBookDetails(bookId);
    }
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <TheNavbar />
        <main class="max-w-5xl mx-auto p-4 md:p-8">
            <div v-if="loading"
                class="bg-white dark:bg-gray-800 rounded-2xl p-5 md:p-10 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-6 md:gap-12">
                    <div class="w-full md:w-1/4 flex flex-col items-center">
                        <Skeleton width="160px" height="240px" borderRadius="12px" class="md:w-full md:h-[300px]" />
                    </div>
                    <div class="flex-1 flex flex-col gap-4">
                        <div class="flex justify-between items-start gap-4">
                            <Skeleton width="70%" height="2rem" />
                            <Skeleton width="4rem" height="2rem" borderRadius="6px" />
                        </div>
                        <Skeleton width="40%" height="1.5rem" />
                        <Skeleton width="20%" height="1rem" class="mt-2" />
                        <div class="mt-6 grid grid-cols-2 gap-6">
                            <Skeleton width="100%" height="3rem" />
                            <Skeleton width="100%" height="3rem" />
                            <Skeleton width="100%" height="3rem" />
                        </div>
                    </div>
                </div>
            </div>
            <div v-else-if="book"
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-5 md:p-10">
                    <div class="flex flex-col md:flex-row gap-6 md:gap-12">
                        <div class="w-full md:w-1/4 flex flex-col items-center shrink-0">
                            <div
                                class="relative w-40 md:w-full max-w-[220px] aspect-[2/3] shadow-lg md:shadow-2xl rounded-lg overflow-hidden group">
                                <img v-if="book.image_url" :src="getCleanCoverUrl(book.image_url)" :alt="book.title"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                <div v-else
                                    class="w-full h-full bg-gray-200 dark:bg-gray-700 flex flex-col items-center justify-center text-gray-400">
                                    <i class="pi pi-image text-3xl md:text-4xl mb-2"></i>
                                    <span class="text-xs">Sem Capa</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex-1 text-left">
                            <div class="flex justify-between items-start gap-3 mb-2">
                                <h2
                                    class="text-2xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight flex-1">
                                    {{ book.title }}
                                </h2>
                                <Button label="Voltar" icon="pi pi-arrow-left" text @click="goBack"
                                    class="shrink-0 !px-2 md:!px-3 !py-1 md:!py-2 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 text-sm md:text-base" />
                            </div>
                            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-3 md:mb-4">
                                de <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{
                                    book.authors?.join(', ') || 'Desconhecido' }}</span>
                            </p>
                            <div class="flex items-center gap-3 mb-6" v-if="book.average_rating">
                                <Rating :modelValue="book.average_rating" readonly :cancel="false" />
                                <span class="text-xs md:text-sm text-gray-500 dark:text-gray-400">
                                    ({{ book.ratings_count || 0 }})
                                </span>
                            </div>
                            <Divider class="my-5 md:my-6" />
                            <div class="grid grid-cols-2 gap-y-4 md:gap-y-6 gap-x-4 text-sm mb-2">
                                <div>
                                    <span
                                        class="block text-gray-400 uppercase text-[10px] md:text-xs font-bold mb-1">Editora</span>
                                    <span
                                        class="text-gray-800 dark:text-white font-medium text-base md:text-lg block truncate"
                                        :title="book.publisher">
                                        {{ book.publisher || '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span
                                        class="block text-gray-400 uppercase text-[10px] md:text-xs font-bold mb-1">Publicação</span>
                                    <span class="text-gray-800 dark:text-white font-medium text-base md:text-lg">
                                        {{ formatDate(book.published_date) || '-' }}
                                    </span>
                                </div>
                                <div>
                                    <span
                                        class="block text-gray-400 uppercase text-[10px] md:text-xs font-bold mb-1">Páginas</span>
                                    <span class="text-gray-800 dark:text-white font-medium text-base md:text-lg">
                                        {{ book.page_count || '-' }}
                                    </span>
                                </div>
                                <div v-if="book.isbn">
                                    <span
                                        class="block text-gray-400 uppercase text-[10px] md:text-xs font-bold mb-1">ISBN</span>
                                    <span class="text-gray-800 dark:text-white font-mono text-sm md:text-base">
                                        {{ book.isbn }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/50 p-5 md:p-10 border-t border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mb-3 md:mb-4 flex items-center gap-2">
                        <i class="pi pi-align-left text-indigo-500"></i> Sinopse
                    </h3>
                    <div v-if="book.description"
                        class="text-gray-600 dark:text-gray-300 leading-relaxed text-base md:text-lg description-content"
                        v-html="book.description">
                    </div>
                    <p v-else class="text-gray-400 italic">Nenhuma descrição disponível para este livro.</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-800/50 p-5 md:p-10 border-t border-gray-100 dark:border-gray-700">
                    <h3
                        class="text-lg md:text-xl font-bold text-gray-800 dark:text-white mb-3 md:mb-4 flex items-center gap-2">
                        <i class="pi pi-bookmark text-indigo-500"></i> Categorias
                    </h3>
                    <div v-if="book.categories" class="flex flex-wrap gap-2 justify-start mb-1">
                        <Tag v-for="cat in book.categories" :key="cat" :value="cat" severity="secondary"
                            class="!bg-gray-100 dark:!bg-gray-700 !text-gray-600 dark:!text-gray-300" />
                    </div>
                    <p v-else class="text-gray-400 italic">Esse livro não foi classificado por gêneros.</p>
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