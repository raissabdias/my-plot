<script setup>
import { ref, onMounted } from 'vue';
import BookService from './services/BookService';

import TheNavbar from './components/TheNavbar.vue';
import BookForm from './components/Books/BookForm.vue';
import BookList from './components/Books/BookList.vue';

import Dialog from 'primevue/dialog';

const books = ref([]);
const isModalVisible = ref(false);
const selectedBook = ref(null);

const fetchBooks = async () => {
    try {
        const response = await BookService.getAll();
        books.value = response.data;
    } catch (error) {
        console.error("Error fetching books:", error);
    }
};

const openEditModal = (book) => {
    selectedBook.value = book;
    isModalVisible.value = true;
};

const onBookSaved = () => {
    isModalVisible.value = false;
    selectedBook.value = null;
    fetchBooks();
};

onMounted(() => {
    fetchBooks();
});
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    <TheNavbar @open-modal="openCreateModal" />
    <main class="max-w-7xl mx-auto p-6">
        <BookList :books="books" @edit="openEditModal" />
    </main>
    <Dialog 
        v-model:visible="isModalVisible" 
        modal 
        :header="selectedBook ? 'Editar Livro' : 'Adicionar Novo Livro'" 
        :style="{ width: '50rem' }" 
        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
        dismissableMask
        class="dark:bg-gray-900"
    >
        <div class="pt-2">
            <BookForm 
                :bookToEdit="selectedBook" 
                @created="onBookSaved" 
                @updated="onBookSaved"
                class="!shadow-none !border-0 !p-0" 
            />
        </div>
    </Dialog>
  </div>
</template>