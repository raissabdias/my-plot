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
import Button from 'primevue/button';

import { useToast } from 'primevue/usetoast';

const loading = ref(true);
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

        const response = await BookService.getMyShelf(params);
        books.value = response.data.data;
        totalRecords.value = response.data.total;
    } catch (error) {
        console.error("Error fetching books:", error);
    } finally {
        loading.value = false;
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
                await BookService.removeFromShelf(book.id);
                toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Livro excluído com sucesso.', life: 3000 });
                fetchBooks();
            } catch (error) {
                console.error("Erro ao excluir", error);
                toast.add({ severity: 'error', summary: 'Erro', detail: 'Não foi possível excluir o livro.', life: 3000 });
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
        <TheNavbar @logout="handleLogout" />
        <main class="max-w-7xl mx-auto p-4 md:p-6">
            <div class="flex justify-between items-center gap-4 mb-6">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2">
                        Minha Estante
                    </h1>
                </div>
                <Button @click="openCreateModal" severity="primary" raised class="!p-3 md:!px-4 md:!py-2">
                    <div class="flex items-center gap-2">
                        <i class="pi pi-plus"></i>
                        <span class="hidden md:inline">Novo Livro</span>
                    </div>
                </Button>
            </div>
            <div class="bg-white dark:bg-gray-800 p-3 md:p-4 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 mb-6 flex flex-col-reverse md:flex-row gap-3 justify-between items-center">
                <div class="w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                    <SelectButton v-model="filters.status" :options="statusOptions" optionLabel="label"
                        optionValue="value" class="whitespace-nowrap min-w-max" />
                </div>
                <div class="flex gap-4 items-center w-full md:w-auto">
                    <span class="hidden md:block text-sm text-gray-500 whitespace-nowrap" v-if="!loading">
                        <span v-if="totalRecords > 0"><strong>{{ totalRecords }}</strong> livro(s)</span>
                        <span v-else>Nenhum livro</span>
                    </span>
                    <IconField iconPosition="left" class="w-full md:w-64">
                        <InputIcon class="pi pi-search" />
                        <InputText v-model="filters.search" placeholder="Buscar..." class="w-full" />
                    </IconField>
                </div>
            </div>
            <div v-if="loading" class="flex justify-center py-20">
                <ProgressSpinner strokeWidth="4" />
            </div>
            <div v-else>
                <BookList :books="books" :loading="loading" @edit="openEditModal" @delete="confirmDelete" />
                <div v-if="totalRecords > 0" class="mt-6 flex justify-center">
                    <Paginator :rows="rowsPerPage" :totalRecords="totalRecords" @page="onPageChange"
                        template="PrevPageLink PageLinks NextPageLink" class="!bg-transparent !border-0" />
                </div>
                <div v-if="totalRecords === 0 && !filters.search && !filters.status"
                    class="text-center py-10 text-gray-500">
                    <i class="pi pi-folder-open text-4xl mb-3 block"></i>
                    <p>Sua estante está vazia. Adicione seu primeiro livro!</p>
                </div>
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
</template>