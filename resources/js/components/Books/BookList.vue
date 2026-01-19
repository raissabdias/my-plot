<script setup>
import Tag from 'primevue/tag';
import Button from 'primevue/button';
import Rating from 'primevue/rating';
import Skeleton from 'primevue/skeleton';

const emit = defineEmits(['edit', 'delete', 'updated']);

const props = defineProps({
    books: {
        type: Array,
        required: true
    },
    loading: {
        type: Boolean,
        required: false,
        default: false
    }
});

const getStatusColor = (color) => {
    switch (color) {
        case 'green':
            return '!bg-emerald-600 !text-white border-emerald-500';
        case 'blue':
            return '!bg-blue-600 !text-white border-blue-500';
        case 'gray':
            return '!bg-gray-700 !text-white border-gray-600';
        default:
            return '!bg-gray-500 !text-white';
    }
};
</script>

<template>
    <div v-if="loading" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-6">
        <div v-for="i in 4" :key="i"
            class="bg-white rounded-xl shadow-sm flex flex-col overflow-hidden border border-gray-100 dark:bg-gray-800 dark:border-gray-700 h-full">
            <div class="relative w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700">
                <Skeleton width="100%" height="100%" class="!rounded-none" />
                <div class="absolute top-2 right-2 z-10">
                    <Skeleton width="3rem" height="1.25rem" borderRadius="4px" />
                </div>
            </div>
            <div class="p-4 flex flex-col flex-1">
                <div class="min-h-[2.5rem] mb-1 flex flex-col gap-1">
                    <Skeleton width="100%" height="1rem" />
                    <Skeleton width="60%" height="1rem" />
                </div>
                <Skeleton width="40%" height="0.75rem" class="mb-3 mt-1" />
                <div
                    class="mt-auto pt-3 border-t border-gray-50 flex justify-between items-center dark:border-gray-700 h-10">
                    <div class="flex items-center">
                        <Skeleton width="5rem" height="1rem" />
                    </div>
                    <div class="flex items-center gap-1">
                        <Skeleton shape="circle" size="2rem" class="mr-1" />
                        <Skeleton shape="circle" size="2rem" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 md:gap-6">
        <div v-for="book in books" :key="book.id" @click="$emit('edit', book)"
            class="group bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden border border-gray-100 dark:bg-gray-800 dark:border-gray-700">
            <div class="relative w-full aspect-[2/3] bg-gray-100 dark:bg-gray-700">
                <img v-if="book.image_url" :src="book.image_url"
                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    alt="Capa do livro" />
                <div v-else
                    class="w-full h-full flex flex-col items-center justify-center text-gray-400 p-4 text-center">
                    <i class="pi pi-image text-4xl mb-2"></i>
                    <span class="text-xs">Sem Capa</span>
                </div>
                <div class="absolute top-2 right-2 z-10">
                    <Tag :value="book.status_formatted.label"
                        class="shadow-lg !text-[10px] px-2 font-bold !text-white border border-white/20"
                        :class="getStatusColor(book.status_formatted.color)" />
                </div>
            </div>
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-gray-800 text-base leading-tight mb-1 line-clamp-2 min-h-[2.5rem] dark:text-gray-100"
                    :title="book.title">
                    {{ book.title }}
                </h3>
                <p class="text-gray-500 text-xs mb-3 truncate">{{ book.author }}</p>
                <div
                    class="mt-auto pt-3 border-t border-gray-50 flex justify-between items-center dark:border-gray-700 h-10">
                    <div class="flex items-center" @click.stop>
                        <Rating :modelValue="book.rating" readonly :cancel="false"
                            class="!gap-0.5 scale-75 origin-left" />
                    </div>
                    <div class="flex items-center gap-1">
                        <div v-if="book.review"
                            class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors cursor-help"
                            v-tooltip.top="book.review">
                            <i class="pi pi-comment text-indigo-400 text-sm"></i>
                        </div>
                        <Button icon="pi pi-trash" text rounded severity="danger" class="!w-8 !h-8"
                            @click.stop="$emit('delete', book)" />
                    </div>
                </div>
            </div>
        </div>
        <div v-if="books.length === 0"
            class="col-span-full flex flex-col items-center justify-center py-16 text-gray-400 bg-white rounded-xl border border-dashed border-gray-300 dark:bg-gray-800 dark:border-gray-700">
            <p class="text-lg">Sua estante está vazia.</p>
            <p class="text-sm">Use a busca acima para adicionar seu primeiro livro!</p>
        </div>
    </div>
</template>