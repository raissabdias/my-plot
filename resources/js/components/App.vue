<script setup>
import { ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import AutoComplete from 'primevue/autocomplete'; // <--- O Novo Componente
import axios from 'axios';

const books = ref([]);

const selectedBookSearch = ref(null); // O objeto que o usuário selecionou no dropdown
const suggestions = ref([]); // A lista de opções que aparece enquanto digita

const title = ref('');
const author = ref('');
const isbn = ref('');
const imageUrl = ref('');

// Buscar livros do Google Books API enquanto o usuário digita
const searchBookParams = async (event) => {
    const query = event.query;

    try {
        const response = await axios.get('/api/books/search', {
            params: { q: query }
        });
        suggestions.value = response.data;
    } catch (error) {
        console.error(error);
        suggestions.value = [];
    }
};

// Selecionar um livro da lista de sugestões
const onBookSelect = (event) => {
    const book = event.value;

    title.value = book.title;
    author.value = book.author;
    isbn.value = book.isbn;
    imageUrl.value = book.image_url;

    selectedBookSearch.value = null;
};

// Lista de livros salvos
const fetchBooks = async () => {
    try {
        const response = await axios.get('/api/books');
        books.value = response.data;
    } catch (error) {
        console.error("Error fetching books:", error);
    }
};

// Salvar um novo livro
const saveBook = async () => {
    if (!title.value) return;

    try {
        await axios.post('/api/books', {
            title: title.value,
            author: author.value,
            isbn: isbn.value,
            image_url: imageUrl.value
        });

        title.value = '';
        author.value = '';
        isbn.value = '';
        imageUrl.value = '';

        await fetchBooks();
    } catch (error) {
        console.error("Error saving book:", error);
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
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Adicionar Novo Livro</h2>
            <div class="flex flex-col gap-2 mb-6">
                <label class="text-sm text-gray-500">Busque no Google Books:</label>
                <AutoComplete v-model="selectedBookSearch" :suggestions="suggestions" @complete="searchBookParams"
                    @item-select="onBookSelect" optionLabel="title" placeholder="Digite o nome do livro e selecione..."
                    class="w-full" inputClass="w-full">
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <img v-if="slotProps.option.image_url" :src="slotProps.option.image_url"
                                class="w-8 h-12 object-cover" />
                            <div class="flex flex-col">
                                <span class="font-bold">{{ slotProps.option.title }}</span>
                                <span class="text-xs text-gray-500">{{ slotProps.option.author }}</span>
                            </div>
                        </div>
                    </template>
                </AutoComplete>
            </div>
            <hr class="mb-6 border-gray-200" />
            <div class="flex gap-6">
                <div class="w-32 flex-shrink-0 flex flex-col gap-2">
                    <div v-if="imageUrl" class="relative">
                        <img :src="imageUrl" alt="Capa" class="w-full rounded shadow-md" />
                    </div>
                    <div v-else
                        class="w-full h-44 bg-gray-100 rounded flex items-center justify-center text-gray-400 text-xs text-center p-2">
                        Sem Capa
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 text-sm">Título</label>
                        <InputText v-model="title" class="w-full" />
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-1 flex flex-col gap-2">
                            <label class="font-semibold text-gray-600 text-sm">Autor</label>
                            <InputText v-model="author" class="w-full" />
                        </div>
                        <div class="w-1/3 flex flex-col gap-2">
                            <label class="font-semibold text-gray-600 text-sm">ISBN</label>
                            <InputText v-model="isbn" class="w-full bg-gray-50" readonly />
                        </div>
                    </div>
                    <div class="mt-2">
                        <Button label="Confirmar e Salvar" icon="pi pi-check" severity="success" @click="saveBook"
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
                <div class="flex flex-col justify-between py-1 w-full">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800 leading-tight mb-1">{{ book.title }}</h3>
                        <p class="text-gray-600 text-sm">{{ book.author }}</p>
                        <p v-if="book.isbn" class="text-gray-400 text-xs mt-1">ISBN: {{ book.isbn }}</p>
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