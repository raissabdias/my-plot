<script setup>
import Tag from 'primevue/tag';

const props = defineProps({
    books: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-for="book in books" :key="book.id" class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm flex gap-4 hover:shadow-md transition-shadow">
            
            <img v-if="book.image_url" :src="book.image_url" class="w-20 h-28 object-cover rounded shadow-sm" />
            <div v-else class="w-20 h-28 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                <i class="pi pi-image text-2xl"></i>
            </div>

            <div class="flex flex-col justify-between py-1 w-full">
                <div>
                    <h3 class="font-bold text-lg text-gray-800 leading-tight mb-1">{{ book.title }}</h3>
                    <p class="text-gray-600 text-sm">{{ book.author }}</p>
                    <p v-if="book.isbn" class="text-gray-400 text-xs mt-1">ISBN: {{ book.isbn }}</p>
                </div>
                
                <div class="mt-2">
                    <Tag 
                        :value="book.status_formatted.label" 
                        :severity="book.status_formatted.color" 
                        :icon="book.status_formatted.icon" 
                    />
                </div>
            </div>
        </div>
        
        <div v-if="books.length === 0" class="col-span-full text-center py-10 text-gray-500">
            Nenhum livro encontrado. Adicione o primeiro acima!
        </div>
    </div>
</template>