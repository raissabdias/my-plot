<script setup>
import { ref, watch, computed } from 'vue';
import BookService from '../../services/BookService';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import AutoComplete from 'primevue/autocomplete';
import Rating from 'primevue/rating';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import Checkbox from 'primevue/checkbox';

import { useToast } from 'primevue/usetoast';

const props = defineProps({
    bookToEdit: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['created', 'updated']);
const toast = useToast();

// Dados do Livro
const google_book_id = ref(null);
const title = ref('');
const author = ref('');
const isbn = ref('');
const imageUrl = ref('');
const page_count = ref(0);

// Dados da Estante (Usuário)
const status = ref(null);
const rating = ref(0);
const review = ref('');
const started_at = ref(null);
const finished_at = ref(null);
const is_public = ref(true);

// Busca
const selectedBookSearch = ref(null);
const suggestions = ref([]);
const isLoading = ref(false);

const statusOptions = [
    { label: 'Quero Ler', value: 'planning_to_read' },
    { label: 'Lendo', value: 'reading' },
    { label: 'Lido', value: 'read' }
];

const resetForm = () => {
    google_book_id.value = null;
    title.value = '';
    author.value = '';
    isbn.value = '';
    imageUrl.value = '';
    page_count.value = 0;
    status.value = 'planning_to_read';
    rating.value = 0;
    review.value = '';
    selectedBookSearch.value = null;
    started_at.value = null;
    finished_at.value = null;
    is_public.value = true;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR', { 
        timeZone: 'UTC' 
    });
};

watch(() => props.bookToEdit, (newBook) => {
    if (newBook) {
        console.log("Editando livro:", newBook);
        title.value = newBook.title;
        author.value = newBook.author;
        isbn.value = newBook.isbn;
        imageUrl.value = newBook.image_url;
        status.value = newBook.status;
        rating.value = newBook.rating || 0;
        review.value = newBook.review;
        started_at.value = newBook.started_at ? formatDate(newBook.started_at) : null;
        finished_at.value = newBook.finished_at ? formatDate(newBook.finished_at) : null;
        google_book_id.value = newBook.google_book_id || null;
        is_public.value = newBook.is_public ? true : false;
    } else {
        resetForm();
    }
}, { immediate: true });

const searchBookParams = async (event) => {
    try {
        const response = await BookService.searchGoogle(event.query);
        suggestions.value = response.data;
    } catch (error) {
        suggestions.value = [];
    }
};

const onBookSelect = (event) => {
    const book = event.value;
    title.value = book.title;
    author.value = book.author;
    isbn.value = book.isbn;
    imageUrl.value = book.image_url;
    google_book_id.value = book.id;
    page_count.value = book.page_count || 0;

    selectedBookSearch.value = null;
};

const saveBook = async () => {
    if (!title.value) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'O título é obrigatório.', life: 3000 });
        return;
    }

    if (!status.value) {
        toast.add({ severity: 'warn', summary: 'Atenção', detail: 'Selecione um status de leitura.', life: 3000 });
        return;
    }

    const payload = {
        google_book_id: google_book_id.value,
        title: title.value,
        authors: author.value,
        isbn: isbn.value,
        image_url: imageUrl.value,
        page_count: page_count.value,
        status: status.value,
        rating: rating.value,
        review: review.value,
        started_at: started_at.value,
        finished_at: finished_at.value,
        is_public: is_public.value
    };

    try {
        isLoading.value = true;

        if (props.bookToEdit) {
            await BookService.updateShelf(props.bookToEdit.id, payload);
            emit('updated');
            toast.add({ severity: 'success', summary: 'Atualizado', detail: 'Livro atualizado com sucesso.', life: 3000 });
        }
        else {
            await BookService.addToShelf(payload);
            emit('created');
            toast.add({ severity: 'success', summary: 'Adicionado', detail: 'Livro adicionado à estante!', life: 3000 });
        }

        resetForm();

    } catch (error) {
        console.error("Erro ao salvar:", error);
        const errorMsg = error.response?.data?.message || 'Não foi possível salvar o livro.';
        toast.add({ severity: 'error', summary: 'Erro', detail: errorMsg, life: 3000 });
    } finally {
        isLoading.value = false;
    }
};

const buttonLabel = computed(() => {
    if (isLoading.value) return 'Salvando...';
    return props.bookToEdit ? 'Atualizar Livro' : 'Adicionar à Estante';
});
</script>

<template>
    <div class="flex flex-col gap-6 dark:bg-gray-900">
        <div v-if="!bookToEdit" class="flex flex-col gap-2">
            <label class="text-sm text-gray-500 dark:text-gray-400">Preenchimento automático via Google:</label>
            <AutoComplete v-model="selectedBookSearch" :suggestions="suggestions" @complete="searchBookParams"
                @item-select="onBookSelect" optionLabel="title" placeholder="Busque o livro..." class="w-full"
                inputClass="w-full" panelClass="book-search-dropdown">
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
            <div class="border-b border-gray-200 dark:border-gray-700 my-2"></div>
        </div>
        <div class="flex gap-6 flex-col md:flex-row">
            <div class="w-full md:w-32 flex-shrink-0 flex flex-col gap-2 items-center">
                <div v-if="imageUrl" class="relative w-32 aspect-[2/3]">
                    <img :src="imageUrl" alt="Capa" class="w-full h-full object-cover rounded shadow-md" />
                </div>
                <div v-else
                    class="w-32 aspect-[2/3] bg-gray-100 dark:bg-gray-800 rounded flex items-center justify-center text-gray-400 text-xs text-center p-2">
                    Sem Capa
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-4 w-full">
                <div class="flex flex-col gap-2">
                    <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Título</label>
                    <InputText v-model="title" class="w-full" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Autor</label>
                        <InputText v-model="author" class="w-full" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">ISBN</label>
                        <InputText v-model="isbn" class="w-full bg-gray-50 dark:bg-gray-800 dark:text-gray-400" />
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Status de Leitura</label>
                        <Dropdown v-model="status" :options="statusOptions" optionLabel="label" optionValue="value"
                            placeholder="Selecione..." class="w-full" />
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Sua Avaliação</label>
                        <div class="h-[42px] flex items-center">
                            <Rating v-model="rating" :cancel="false" />
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-2">
                    <div v-if="status === 'read' || status === 'reading'" class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Início da Leitura</label>
                        <DatePicker v-model="started_at" dateFormat="dd/mm/yy" class="w-full" />
                    </div>
                    <div v-if="status === 'read'" class="flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Término da Leitura</label>
                        <DatePicker v-model="finished_at" dateFormat="dd/mm/yy" class="w-full" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-semibold text-gray-600 dark:text-gray-300 text-sm">Resenha/Comentário</label>
                    <Textarea v-model="review" class="w-full bg-gray-50 dark:bg-gray-800 dark:text-gray-400" rows="2"
                        cols="30" />
                </div>
                <div class="flex items-center gap-2 mt-3">
                    <Checkbox v-model="is_public" :binary="true" inputId="public-review" />
                    <label for="public-review" class="text-sm text-gray-600 dark:text-gray-400 cursor-pointer select-none">
                        Publicar minha resenha na página do livro (apenas após concluir a leitura)
                    </label>
                </div>
                <div class="mt-4 text-center">
                    <Button :label="buttonLabel" icon="pi pi-check" severity="success" @click="saveBook" />
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.book-search-dropdown {
    max-width: 350px !important;
    width: 100%;
}

.book-search-dropdown .p-autocomplete-item {
    white-space: normal !important;
    line-height: 1.4;
    padding: 10px;
    border-bottom: 1px solid #f0f0f0;
}
</style>