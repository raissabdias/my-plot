<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';

import BookService from '../services/BookService';
import AuthService from '../services/AuthService';

import TheNavbar from '../components/TheNavbar.vue';
import BookForm from '../components/Books/BookForm.vue';
import BookList from '../components/Books/BookList.vue';

import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import SelectButton from 'primevue/selectbutton';
import Dialog from 'primevue/dialog';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from "primevue/useconfirm";
import Paginator from 'primevue/paginator';

import { useToast } from 'primevue/usetoast';

const books = ref([]);
const isModalVisible = ref(false);
const selectedBook = ref(null);

const router = useRouter();
const toast = useToast();

const rowsPerPage = ref(10);

const fetchBooks = async () => {
    try {
        const params = {
            ...filters.value,
            per_page: rowsPerPage.value 
        }

        const response = await BookService.getAll(params);
        books.value = response.data.data;
        totalRecords.value = response.data.total;
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
    // Variar a quantidade de linhas por página com base no tamanho da tela (mobile)
    if (window.innerWidth < 768) {
        rowsPerPage.value = 4;
    } else {
        rowsPerPage.value = 10;
    }

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
                toast.add({severity:'success', summary: 'Sucesso', detail: 'Livro excluído com sucesso.', life: 3000});
                fetchBooks();
            } catch (error) {
                console.error("Erro ao excluir", error);
                toast.add({severity:'error', summary: 'Erro', detail: 'Não foi possível excluir o livro.', life: 3000});
            }
        },
    });
};

const totalRecords = ref(0);

const filters = ref({
    status: null,
    search: '',
    page: 1,
});

// Filtrar livros quando os filtros/busca mudarem
watch(
    [() => filters.value.search, () => filters.value.status],
    () => {
        filters.value.page = 1;
        fetchBooks();
    }
);

// Alterar página
watch(
    () => filters.value.page,
    () => {
        fetchBooks();
    }
);

const statusOptions = ref([
    { label: 'Todos', value: null },
    { label: 'Lendo', value: 'reading' },
    { label: 'Quero Ler', value: 'planning_to_read' },
    { label: 'Lido', value: 'read' }
]);

const handleLogout = () => {
    AuthService.logout();
    router.push('/login');
};

const onPageChange = (event) => {
    filters.value.page = event.page + 1;
    fetchBooks();
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300">
        <TheNavbar @open-modal="openCreateModal" @logout="handleLogout" />
        <main class="max-w-7xl mx-auto p-6">
            <div class="flex flex-col md:flex-row justify-between gap-4 mb-8">
                <span class="text-sm text-gray-600 dark:text-gray-400 pt-4">
                    <span v-if="totalRecords > 0">
                        Total de <strong>{{ totalRecords }}</strong> livro(s) encontrado(s).
                    </span>
                    <span v-else>
                        Nenhum livro encontrado.
                    </span>
                </span>
                <SelectButton v-model="filters.status" :options="statusOptions" optionLabel="label" optionValue="value" aria-labelledby="basic" class="w-full md:w-auto" />
                <IconField iconPosition="left" class="w-full md:w-64">
                    <InputIcon class="pi pi-search"> </InputIcon>
                    <InputText v-model="filters.search" placeholder="Buscar na estante..." class="w-full" />
                </IconField>
            </div>
            <BookList :books="books" @edit="openEditModal" @delete="confirmDelete" />
            <div v-if="totalRecords > 0" class="mt-6 flex justify-center">
                <Paginator :rows="rowsPerPage" :totalRecords="totalRecords" @page="onPageChange" template="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink" />
            </div>
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