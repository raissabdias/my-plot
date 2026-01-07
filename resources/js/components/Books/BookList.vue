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
    <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
        <div v-for="book in books" :key="book.id" class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
            <div class="relative w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700">
                <img 
                    v-if="book.image_url" 
                    :src="book.image_url" 
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
                    alt="Capa do livro"
                />
                <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                    <i class="pi pi-image text-4xl mb-2"></i>
                    <span class="text-xs">Sem Capa</span>
                </div>
                <div class="absolute top-2 right-2">
                     <Tag 
                        :value="book.status_formatted.label" 
                        :severity="book.status_formatted.color"
                        class="shadow-md !text-[10px] px-2"
                    />
                </div>
            </div>
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-gray-800 text-base leading-tight mb-1 line-clamp-2 min-h-[2.5rem] dark:text-gray-100" :title="book.title">
                    {{ book.title }}
                </h3>
                <p class="text-gray-500 text-xs mb-3 truncate">{{ book.author }}</p>
                <div class="mt-auto pt-3 border-t border-gray-50 flex justify-between items-center">
                   <span class="text-[10px] text-gray-400 font-mono">
                       {{ book.isbn ?? 'N/I' }}
                   </span>
                   <i :class="[book.status_formatted.icon, 'text-gray-500']"></i>
                </div>
            </div>
        </div>
        <div v-if="books.length === 0" class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400 bg-white rounded-xl border border-dashed border-gray-300 dark:bg-gray-800 dark:border-gray-700">
            <p class="text-lg">Sua estante está vazia.</p>
            <p class="text-sm">Use a busca acima para adicionar seu primeiro livro!</p>
        </div>
    </div>
</template>