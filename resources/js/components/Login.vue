<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import logoLight from '../assets/logo_white.png';
import logoDark from '../assets/logo_black.png';

import AuthService from '../services/AuthService'; 

const email = ref('');
const password = ref('');
const loading = ref(false);
const errorMessage = ref('');

const router = useRouter();

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = '';

    try {
        await AuthService.login(email.value, password.value);
        router.push('/'); 

    } catch (error) {
        console.error(error);
        errorMessage.value = 'E-mail ou senha incorretos.';
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100 dark:border-gray-700">
            <div class="text-center mb-8">
                <router-link to="/">
                    <img :src="logoDark" alt="My Plot Logo" class="h-16 mx-auto mb-4 block dark:hidden" />
                    <img :src="logoLight" alt="My Plot Logo" class="h-16 mx-auto mb-4 hidden dark:block" />
                </router-link>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Acesse sua biblioteca pessoal</p>
            </div>
            <form @submit.prevent="handleLogin" class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">E-mail</label>
                    <InputText v-model="email" type="email" placeholder="seu@email.com" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Senha</label>
                    <Password v-model="password" toggleMask :feedback="false" placeholder="••••••••" class="w-full" inputClass="w-full" />
                </div>
                <div v-if="errorMessage" class="text-red-500 text-sm text-center font-bold mb-2">
                    {{ errorMessage }}
                </div>
                <Button type="submit" label="Entrar na Estante" icon="pi pi-sign-in" :loading="loading" class="w-full mt-2" />
            </form>
            <div class="mt-6 text-center text-sm text-gray-500">
                Não tem conta? <router-link to="/register" class="text-indigo-600 font-bold hover:underline">Cadastre-se</router-link>
            </div>
        </div>
    </div>
</template>