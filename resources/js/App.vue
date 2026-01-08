<script setup>
import { ref, onMounted } from 'vue';
import BookService from './services/BookService';

import TheNavbar from './components/TheNavbar.vue';
import BookForm from './components/Books/BookForm.vue';
import BookList from './components/Books/BookList.vue';

import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

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

const openCreateModal = () => {
    selectedBook.value = null;
    isModalVisible.value = true;
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

const confirm = useConfirm();

const confirmDelete = (book) => {
    confirm.require({
        message: `Tem certeza que deseja excluir "${book.title}"?`,
        header: 'Confirmação de Exclusão',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancelar',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Excluir',
            severity: 'danger'
        },
        accept: async () => {
            
            try {
                await BookService.delete(book.id);
                fetchBooks();
            } catch (error) {
                console.error("Erro ao excluir", error);
            }
        },
    });
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
    <TheNavbar @open-modal="openCreateModal" />
    <main class="max-w-7xl mx-auto p-6">
        <BookList :books="books" @edit="openEditModal" @delete="confirmDelete" />
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
    <ConfirmDialog class="max-w-md" />
  </div>
    <button 
        @click="openCreateModal"
        class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg flex items-center justify-center text-2xl z-50 md:hidden transition-transform active:scale-90"
        aria-label="Adicionar Livro"
    >
        <i class="pi pi-plus"></i>
    </button>
</template>