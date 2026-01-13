<script setup>
import { ref, onMounted, watch } from 'vue';
import BookService from './services/BookService';
import AuthService from './services/AuthService';

import TheNavbar from './components/TheNavbar.vue';
import BookForm from './components/Books/BookForm.vue';
import BookList from './components/Books/BookList.vue';
import Login from './components/Login.vue';
import Register from './components/Register.vue';

import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import SelectButton from 'primevue/selectbutton';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";

const books = ref([]);
const isModalVisible = ref(false);
const selectedBook = ref(null);

const fetchBooks = async () => {
    try {
        const response = await BookService.getAll(filters.value);
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

const isLoggedIn = ref(AuthService.isAuthenticated());

const onLoginSuccess = () => {
    isLoggedIn.value = true;
    showRegister.value = false;

    fetchBooks();
};

const onLogout = () => {
    isLoggedIn.value = false;
};

// TODO: criar roteador
const showRegister = ref(false);

const filters = ref({
    search: ''
});

watch(filters, () => {
    fetchBooks();
}, { deep: true });

const statusOptions = ref([
    { label: 'Todos', value: null },
    { label: 'Lendo', value: 'reading' },
    { label: 'Quero Ler', value: 'planning_to_read' },
    { label: 'Lido', value: 'read' }
]);
</script>

<template>
    <Login v-if="!isLoggedIn && !showRegister" @login-success="onLoginSuccess" @goto-register="showRegister = true" />
    <Register v-else-if="showRegister" @login-success="onLoginSuccess" @goto-login="showRegister = false" />
    <div v-else class="min-h-screen bg-slate-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
        <TheNavbar @open-modal="openCreateModal" @logout="onLogout" />
        <main class="max-w-7xl mx-auto p-6">
            <div class="flex flex-col md:flex-row justify-end gap-4 mb-8">
                <IconField iconPosition="left" class="w-full md:w-64">
                    <InputIcon class="pi pi-search"> </InputIcon>
                    <InputText v-model="filters.search" placeholder="Buscar na estante..." class="w-full" />
                </IconField>
                <SelectButton v-model="filters.status" :options="statusOptions" optionLabel="label" optionValue="value" aria-labelledby="basic" class="w-full md:w-auto" />
            </div>
            <BookList :books="books" @edit="openEditModal" @delete="confirmDelete" />
        </main>
        <Dialog v-model:visible="isModalVisible" modal :header="selectedBook ? 'Editar Livro' : 'Adicionar Novo Livro'"
            :style="{ width: '50rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" dismissableMask
            class="dark:bg-gray-900">
            <div class="pt-2">
                <BookForm :bookToEdit="selectedBook" @created="onBookSaved" @updated="onBookSaved"
                    class="!shadow-none !border-0 !p-0" />
            </div>
        </Dialog>
        <ConfirmDialog class="max-w-md" />
    </div>
    <button @click="openCreateModal"
        class="fixed bottom-6 right-6 w-14 h-14 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full shadow-lg flex items-center justify-center text-2xl z-50 md:hidden transition-transform active:scale-90"
        aria-label="Adicionar Livro">
        <i class="pi pi-plus"></i>
    </button>
</template>