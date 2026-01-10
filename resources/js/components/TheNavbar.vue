<script setup>
import AuthService from '../services/AuthService';

import Button from 'primevue/button';

import logoDark from '../assets/logo_black.png';
import logoLight from '../assets/logo_white.png';

const emit = defineEmits(['open-modal', 'logout']);

const handleLogout = async () => {
    AuthService.logout();
    emit('logout');
};
</script>

<template>
    <nav
        class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <img :src="logoDark" alt="My Plot Logo" class="h-14 w-auto block dark:hidden" />
                    <img :src="logoLight" alt="My Plot Logo" class="h-14 w-auto hidden dark:block" />
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden md:block">
                        <Button label="Novo Livro" icon="pi pi-plus" severity="primary" raised
                            @click="$emit('open-modal')" />
                    </div>
                    <Button 
                        label="Sair" 
                        icon="pi pi-sign-out" 
                        severity="secondary" 
                        text
                        @click="handleLogout"
                        class="!px-3"
                    />
                </div>
            </div>
        </div>
    </nav>
</template>