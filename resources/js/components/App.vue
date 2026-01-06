<script setup>
import { ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import axios from 'axios';

const books = ref([]);
const searchQuery = ref('');
const loading = ref(false);

const title = ref('');
const author = ref('');
const imageUrl = ref('');

const fetchBooks = async () => {
    try {
        const response = await axios.get('/api/books');
        books.value = response.data;
        console.log(books.value);
    } catch (error) {
        console.error("Error fetching books:", error);
    }
};

const saveBook = async () => {
    if (!title.value) return;

    try {
        await axios.post('/api/books', {
            title: title.value,
            author: author.value,
            image_url: imageUrl.value
        });

        title.value = '';
        author.value = '';
        imageUrl.value = '';
        searchQuery.value = '';

        await fetchBooks();
    } catch (error) {
        console.error("Error saving book:", error);
    }
};

const searchBook = async () => {
    if (!searchQuery.value) return;

    loading.value = true;
    imageUrl.value = '';

    try {
        const response = await axios.get('/api/books/search', {
            params: { q: searchQuery.value }
        });

        const data = response.data;
        title.value = data.title;
        author.value = data.author;
        imageUrl.value = data.image_url;

    } catch (error) {
        alert("Livro não encontrado ou erro na busca.");
        console.error(error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchBooks();
});
</script>

<template>
    <div class="max-w-3xl mx-auto p-6 bg-gray-50 min-h-screen">
        <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">My Plot 📚</h1>
        <div class="bg-white p-6 rounded-xl shadow-lg mb-8 border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Novo Livro</h2>
            <div class="flex gap-2 mb-6">
                <span class="p-input-icon-left w-full">
                    <i class="pi pi-search" />
                    <InputText v-model="searchQuery" placeholder="Digite o ISBN ou Nome do livro..." class="w-full"
                        @keyup.enter="searchBook" />
                </span>
                <Button label="Buscar" icon="pi pi-google" @click="searchBook" :loading="loading" />
            </div>
            <hr class="mb-6 border-gray-200" />
            <div class="flex gap-6">
                <div v-if="imageUrl" class="w-32 flex-shrink-0">
                    <img :src="imageUrl" alt="Capa" class="w-full rounded shadow-md" />
                </div>
                <div class="flex-1 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 text-sm">Título</label>
                        <InputText v-model="title" class="w-full" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 text-sm">Autor</label>
                        <InputText v-model="author" class="w-full" />
                    </div>
                    <div class="mt-2">
                        <Button label="Salvar na Biblioteca" icon="pi pi-check" severity="success" @click="saveBook"
                            class="w-full" />
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="book in books" :key="book.id"
                class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex gap-4 hover:shadow-md transition-shadow">
                <img v-if="book.image_url" :src="book.image_url" class="w-20 h-28 object-cover rounded shadow-sm" />
                <div v-else class="w-20 h-28 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                    <i class="pi pi-image text-2xl"></i>
                </div>
                <div class="flex flex-col justify-between py-1">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 leading-tight mb-1">{{ book.title }}</h3>
                        <p class="text-gray-600 text-sm">{{ book.author }}</p>
                    </div>
                    <span
                        class="inline-block px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full w-fit">
                        {{ book.status }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>