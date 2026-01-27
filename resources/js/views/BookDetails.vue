<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';

import BookService from '../services/BookService';
import TheNavbar from '../components/TheNavbar.vue';

const book = ref({});
const loading = ref(false);

const fetchBookDetails = async (id) => {
    try {
        const response = await BookService.getBookDetails(id);
        console.log("Book details:", response.data);
    } catch (error) {
        console.error("Error fetching book:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    const bookId = useRoute().params.id;
    fetchBookDetails(bookId);
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <TheNavbar />
    </div>
</template>