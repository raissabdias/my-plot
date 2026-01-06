<script setup>
import { ref, onMounted } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import axios from 'axios';

const books = ref([]);
const title = ref('');
const author = ref('');

const fetchBooks = async () => {
    try {
        const response = await axios.get('/api/books');
        books.value = response.data;
    } catch (error) {
        console.error("Error fetching books:", error);
    }
};

const saveBook = async () => {
    if (!title.value) return;

    try {
        await axios.post('/api/books', {
            title: title.value,
            author: author.value
        });

        title.value = '';
        author.value = '';

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
    <div class="max-w-2xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">My Plot 📚</h1>
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label for="title" class="font-semibold text-gray-700">Título do Livro</label>
                    <InputText id="title" v-model="title" placeholder="Ex: O Senhor dos Anéis" />
                </div>
                <div class="flex flex-col gap-2">
                    <label for="author" class="font-semibold text-gray-700">Autor</label>
                    <InputText id="author" v-model="author" placeholder="Ex: J.R.R. Tolkien" />
                </div>
                <Button label="Adicionar Livro" icon="pi pi-plus" @click="saveBook" />
            </div>
        </div>
        <div class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-700">Livros Cadastrados:</h2>
            <div v-for="book in books" :key="book.id"
                class="bg-white p-4 rounded border border-gray-200 shadow-sm flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-lg text-gray-800">{{ book.title }}</h3>
                    <p class="text-gray-600 text-sm">{{ book.author }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                    {{ book.status }}
                </span>
            </div>
            <p v-if="books.length === 0" class="text-gray-500 text-center py-4">
                Nenhum livro cadastrado ainda. Comece agora!
            </p>
        </div>
    </div>
</template>