<script setup>
import { ref } from 'vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import AutoComplete from 'primevue/autocomplete';
import BookService from '../../services/BookService';

const emit = defineEmits(['created']);

// Estado local do formulário
const title = ref('');
const author = ref('');
const isbn = ref('');
const imageUrl = ref('');
const selectedBookSearch = ref(null);
const suggestions = ref([]);

// Buscar livros do Google Books API enquanto o usuário digita
const searchBookParams = async (event) => {
    const query = event.query;

    try {
        const response = await BookService.searchGoogle(event.query);
        suggestions.value = response.data;
    } catch (error) {
        console.error(error);
        suggestions.value = [];
    }
};

// Selecionar um livro da lista de sugestões
const onBookSelect = (event) => {
    const book = event.value;

    title.value = book.title;
    author.value = book.author;
    isbn.value = book.isbn;
    imageUrl.value = book.image_url;

    selectedBookSearch.value = null;
};

// Salvar um novo livro
const saveBook = async () => {
    if (!title.value) return;

    try {
        await BookService.create({
            title: title.value,
            author: author.value,
            isbn: isbn.value,
            image_url: imageUrl.value
        });

        title.value = '';
        author.value = '';
        isbn.value = '';
        imageUrl.value = '';

        emit('created');
    } catch (error) {
        console.error("Erro ao salvar:", error);
    }
};
</script>

<template>
    <div class="bg-white">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Adicionar Novo Livro</h2>
        <div class="flex flex-col gap-2 mb-6">
            <label class="text-sm text-gray-500">Busque no Google Books:</label>
            <AutoComplete v-model="selectedBookSearch" :suggestions="suggestions" @complete="searchBookParams"
                @item-select="onBookSelect" optionLabel="title" placeholder="Digite o nome do livro..." class="w-full"
                inputClass="w-full">
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
        </div>
        <hr class="mb-6 border-gray-200" />
        <div class="flex gap-6">
            <div class="w-32 flex-shrink-0 flex flex-col gap-2">
                <div v-if="imageUrl" class="relative">
                    <img :src="imageUrl" alt="Capa" class="w-full rounded shadow-md" />
                </div>
                <div v-else
                    class="w-full h-44 bg-gray-100 rounded flex items-center justify-center text-gray-400 text-xs text-center p-2">
                    Sem Capa
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-semibold text-gray-600 text-sm">Título</label>
                    <InputText v-model="title" class="w-full" />
                </div>
                <div class="flex gap-4">
                    <div class="flex-1 flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 text-sm">Autor</label>
                        <InputText v-model="author" class="w-full" />
                    </div>
                    <div class="w-1/3 flex flex-col gap-2">
                        <label class="font-semibold text-gray-600 text-sm">ISBN</label>
                        <InputText v-model="isbn" class="w-full bg-gray-50" readonly />
                    </div>
                </div>
                <div class="text-center mt-2">
                    <Button label="Salvar" icon="pi pi-check" severity="success" @click="saveBook" />
                </div>
            </div>
        </div>
    </div>
</template>