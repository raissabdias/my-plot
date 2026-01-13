<script setup>
import Toast from 'primevue/toast';
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <Toast :pt="{
            message: {
                class: [
                    'dark:!bg-gray-800',
                    'dark:!border-gray-700'
                ]
            }
        }" />
        <router-view></router-view>
    </div>
</template>