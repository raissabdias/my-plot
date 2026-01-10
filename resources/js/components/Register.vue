<script setup>
import { ref } from 'vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import logoLight from '../assets/logo_white.png';
import logoDark from '../assets/logo_black.png';

import AuthService from '../services/AuthService'; 

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');

const loading = ref(false);
const errorMessage = ref('');
const emit = defineEmits(['login-success', 'goto-login']);

const handleRegister = async () => {
    loading.value = true;
    errorMessage.value = '';

    if (password.value !== confirmPassword.value) {
        errorMessage.value = 'As senhas não coincidem.';
        loading.value = false;
        return;
    }

    try {
        await AuthService.register({ name: name.value, email: email.value, password: password.value, password_confirmation: confirmPassword.value });
        emit('login-success'); 

    } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
             errorMessage.value = error.response.data.message;
        } else {
             errorMessage.value = 'Erro ao registrar. Tente novamente.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 px-4">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100 dark:border-gray-700">
            <div class="text-center mb-8">
                <img :src="logoDark" alt="My Plot Logo" class="h-16 mx-auto mb-4 block dark:hidden" />
                <img :src="logoLight" alt="My Plot Logo" class="h-16 mx-auto mb-4 hidden dark:block" />
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Complete seu cadastro e comece a cadastrar sua estante</p>
            </div>
            <form @submit.prevent="handleRegister" class="flex flex-col gap-5">
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Nome</label>
                    <InputText v-model="name" type="text" placeholder="Seu nome" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">E-mail</label>
                    <InputText v-model="email" type="email" placeholder="seu@email.com" class="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Senha</label>
                    <Password v-model="password" toggleMask :feedback="false" placeholder="••••••••" class="w-full" inputClass="w-full" />
                </div>
                <div class="flex flex-col gap-2">
                    <label class="text-sm font-semibold text-gray-600 dark:text-gray-300">Confirmar Senha</label>
                    <Password v-model="confirmPassword" toggleMask :feedback="false" placeholder="••••••••" class="w-full" inputClass="w-full" />
                </div>
                <div v-if="errorMessage" class="text-red-500 text-sm text-center font-bold mb-2">
                    {{ errorMessage }}
                </div>
                <Button type="submit" label="Cadastrar" icon="pi pi-user-plus" :loading="loading" class="w-full mt-2" />
            </form>
            <div class="mt-6 text-center text-sm text-gray-500">
                Já tem uma conta? <a href="#" @click.prevent="emit('goto-login')" class="text-indigo-600 font-bold hover:underline">Faça login</a>
            </div>
        </div>
    </div>
</template>