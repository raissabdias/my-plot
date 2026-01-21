<script setup>  
import { ref, onMounted } from 'vue';

import TheNavbar from '../components/TheNavbar.vue';

const user = ref({});
const defaultAvatar = 'https://ui-avatars.com/api/?name=User&background=0D8ABC&color=fff';

onMounted(() => {
    const userData = localStorage.getItem('user_data');
    if (userData) {
        user.value = JSON.parse(userData);
    }
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <TheNavbar />
        <main class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-2xl overflow-hidden">
                <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="px-6 pb-8">
                    <div class="relative flex items-end -mt-12 mb-6">
                        <div class="relative">
                            <img :src="user.avatar || defaultAvatar" alt="Avatar" class="w-32 h-32 rounded-full border-4 border-white dark:border-gray-800 object-cover bg-white">
                            <button
                                class="absolute bottom-0 right-0 bg-indigo-600 p-2 rounded-full text-white hover:bg-indigo-700 transition shadow-lg">
                                <i class="pi pi-camera"></i>
                            </button>
                        </div>
                        <div class="ml-6 mb-2">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
                            <p class="text-gray-500 dark:text-gray-400">{{ user.email }}</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Minha Conta</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nome
                                    Completo</label>
                                <div class="mt-1 text-gray-900 dark:text-white font-medium">{{ user.name }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                                <div class="mt-1 text-gray-900 dark:text-white font-medium">{{ user.email }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Membro
                                    desde</label>
                                <div class="mt-1 text-gray-900 dark:text-white font-medium">
                                    {{ new Date(user.created_at).toLocaleDateString('pt-BR') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>