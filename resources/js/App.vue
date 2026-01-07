<script setup>
import { ref, onMounted } from 'vue';

import BookForm from './components/Books/BookForm.vue';
import BookList from './components/Books/BookList.vue';
import BookService from './services/BookService';

const books = ref([]);

// Lista de livros salvos
const fetchBooks = async () => {
  try {
    const response = await BookService.getAll();
    books.value = response.data;
  } catch (error) {
    console.error("Error fetching books:", error);
  }
};

onMounted(() => {
  fetchBooks();
});
</script>

<template>
  <div class="max-w-4xl mx-auto p-6 bg-gray-50 min-h-screen">
      <h1 class="text-4xl font-bold text-gray-800 mb-8 text-center">My Plot 📚</h1>
      <BookForm @created="fetchBooks" />
      <BookList :books="books" />
  </div>
</template>