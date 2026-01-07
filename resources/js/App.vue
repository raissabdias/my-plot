<script setup>
import { ref, onMounted } from 'vue';
import BookService from './services/BookService';

import TheNavbar from './components/TheNavbar.vue';
import BookForm from './components/Books/BookForm.vue';
import BookList from './components/Books/BookList.vue';

import Dialog from 'primevue/dialog';

const books = ref([]);
const isModalVisible = ref(false);

// Busca os livros
const fetchBooks = async () => {
    try {
        const response = await BookService.getAll();
        books.value = response.data;
    } catch (error) {
        console.error("Error fetching books:", error);
    }
};

// Quando um livro é criado com sucesso
const onBookCreated = () => {
    isModalVisible.value = false;
    fetchBooks(); 
};

onMounted(() => {
    fetchBooks();
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    <TheNavbar @open-modal="isModalVisible = true" />
    <main class="max-w-7xl mx-auto p-6">
        <div class="flex items-end justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">My Plot</h2>
                <p class="text-gray-500 mt-1">Gerencie suas leituras e descubra novas histórias.</p>
            </div>
            <div class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
                Total: <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ books.length }}</span> livros
            </div>
        </div>
        <BookList :books="books" />
    </main>
    <Dialog 
        v-model:visible="isModalVisible" 
        modal 
        header="Adicionar Novo Livro" 
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        dismissableMask
    >
        <div class="pt-2">
            <BookForm @created="onBookCreated" class="" />
        </div>
    </Dialog>
  </div>
</template>