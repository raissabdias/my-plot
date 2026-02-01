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

const toDatabaseDate = (dateObj) => {
    if (!dateObj) return null;
    if (!(dateObj instanceof Date) || isNaN(dateObj)) return null;

    const year = dateObj.getFullYear();
    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
    const day = String(dateObj.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};

const parseDatabaseDate = (dateString) => {
    if (!dateString) return null;

    const cleanDate = String(dateString).substring(0, 10);
    const [year, month, day] = cleanDate.split('-').map(Number);
    if (!year || !month || !day) return null;

    return new Date(year, month - 1, day);
};

watch(() => props.bookToEdit, (newBook) => {
    if (newBook) {
        title.value = newBook.title;
        author.value = newBook.author;
        isbn.value = newBook.isbn;
        imageUrl.value = newBook.image_url;
        status.value = newBook.status;
        rating.value = newBook.rating || 0;
        review.value = newBook.review;
        started_at.value = newBook.started_at ? parseDatabaseDate(newBook.started_at) : null;
        finished_at.value = newBook.finished_at ? parseDatabaseDate(newBook.finished_at) : null;
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
        started_at: toDatabaseDate(started_at.value),
        finished_at: toDatabaseDate(finished_at.value),
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

const getCleanCoverUrl = (url) => {
    if (!url) return null;
    return url.replace('http://', 'https://').replace('&edge=curl', '');
};
</script>

<template>
    <div class="flex flex-col gap-5 dark:bg-gray-900">
        <div v-if="!bookToEdit"
            class="flex flex-col gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-100 dark:border-gray-700">
            <AutoComplete v-model="selectedBookSearch" :suggestions="suggestions" @complete="searchBookParams"
                @item-select="onBookSelect" optionLabel="title" placeholder="Digite o nome do livro..." class="w-full"
                inputClass="w-full !py-2" panelClass="book-search-dropdown">
                <template #option="slotProps">
                    <div class="flex items-center gap-3">
                        <img v-if="slotProps.option.image_url" :src="slotProps.option.image_url"
                            class="w-8 h-12 object-cover rounded shadow-sm" />
                        <div class="flex flex-col">
                            <span class="font-bold text-sm">{{ slotProps.option.title }}</span>
                            <span class="text-xs text-gray-500">{{ slotProps.option.author }}</span>
                        </div>
                    </div>
                </template>
            </AutoComplete>
        </div>
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-40 flex-shrink-0 flex flex-col gap-3 items-center">
                <div
                    class="relative w-32 md:w-40 aspect-[2/3] shadow-lg rounded overflow-hidden bg-gray-100 dark:bg-gray-800 group border border-gray-200 dark:border-gray-700">
                    <img v-if="imageUrl" :src="getCleanCoverUrl(imageUrl)" alt="Capa"
                        class="w-full h-full object-cover transition-transform group-hover:scale-105" />
                    <div v-else
                        class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-2 text-center">
                        <i class="pi pi-image text-2xl mb-1"></i>
                        <span class="text-[10px]">Sem Capa</span>
                    </div>
                </div>
                <div class="w-full">
                    <label class="text-[10px] font-bold text-gray-400 uppercase text-center block mb-1">ISBN /
                        ID</label>
                    <InputText v-model="isbn"
                        class="w-full !text-xs text-center bg-gray-50 dark:bg-gray-800 opacity-80 !py-1" disabled />
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-3">
                <div>
                    <label class="font-semibold text-gray-700 dark:text-gray-200 text-sm block mb-1">Título</label>
                    <InputText v-model="title" class="w-full" />
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold text-gray-700 dark:text-gray-200 text-sm block mb-1">Autor</label>
                        <InputText v-model="author" class="w-full" />
                    </div>
                    <div>
                        <label class="font-semibold text-gray-700 dark:text-gray-200 text-sm block mb-1">Status</label>
                        <Dropdown v-model="status" :options="statusOptions" optionLabel="label" optionValue="value"
                            placeholder="Selecione..." class="w-full" />
                    </div>
                </div>
                <div v-if="status === 'read' || status === 'reading'" class="mt-1">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="font-bold text-gray-600 dark:text-gray-400 text-xs block mb-1">Início</label>
                            <DatePicker v-model="started_at" dateFormat="dd/mm/yy" showIcon fluid />
                        </div>
                        <div v-if="status === 'read'">
                            <label class="font-bold text-gray-600 dark:text-gray-400 text-xs block mb-1">Término</label>
                            <DatePicker v-model="finished_at" dateFormat="dd/mm/yy" showIcon fluid />
                        </div>
                        <div v-if="status === 'read'">
                            <label class="font-semibold text-gray-700 dark:text-gray-200 text-sm block mb-1">Sua
                                Avaliação</label>
                            <div class="h-10 flex items-center px-2">
                                <Rating v-model="rating" :cancel="false" class="scale-90 origin-left" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-1">
                    <div class="flex justify-between items-end mb-1">
                        <label class="font-semibold text-gray-700 dark:text-gray-200 text-sm">Resenha</label>
                        <span class="text-[10px] text-gray-400">Opcional</span>
                    </div>
                    <Textarea v-model="review" class="w-full text-sm !bg-white dark:!bg-gray-900" rows="2" autoResize
                        placeholder="O que você achou do livro?" />
                    <div
                        class="flex items-start gap-2 mt-2 p-2 rounded hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <Checkbox v-model="is_public" :binary="true" inputId="public-review" class="mt-0.5" />
                        <label for="public-review"
                            class="text-xs text-gray-600 dark:text-gray-300 cursor-pointer select-none leading-tight">
                            Publicar minha resenha na página do livro
                            <span class="block text-[10px] text-gray-400 mt-0.5">(Será visível para outros usuários após a conclusão da leitura conclusão da leitura)</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end pt-2 border-gray-100 dark:border-gray-700 mt-2">
                    <Button :label="buttonLabel" icon="pi pi-check" severity="success" @click="saveBook"
                        class="w-full md:w-auto min-w-[120px]" />
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