<script setup>
import Button from 'primevue/button';

defineEmits(['open-modal']);
</script>

<template>
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-600 text-white p-2 rounded-lg">
                <i class="pi pi-book text-xl"></i>
            </div>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">
                MyPlot
            </h1>
        </div>
        <div>
            <Button 
                label="Novo Livro" 
                icon="pi pi-plus" 
                severity="primary" 
                raised
                @click="$emit('open-modal')" 
            />
        </div>
    </nav>
</template>