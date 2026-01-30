<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import BookService from '../services/BookService';
import TheNavbar from '../components/TheNavbar.vue';

// PrimeVue Components
import Button from 'primevue/button';
import Skeleton from 'primevue/skeleton';
import Tag from 'primevue/tag';

const router = useRouter();
const trendingBooks = ref([]);
const loading = ref(true);

const fetchTrending = async () => {
    try {
        const response = await BookService.searchGoogle('subject:fiction&langRestrict=pt');
        trendingBooks.value = response.data.slice(0, 5);
    } catch (error) {
        console.error("Erro ao carregar destaques", error);
    } finally {
        loading.value = false;
    }
};

const goToBook = (id) => {
    router.push(`/book/${id}`);
};

const getCleanCoverUrl = (url) => {
    if (!url) return null;
    return url.replace('http://', 'https://').replace('&edge=curl', '');
};

onMounted(() => {
    fetchTrending();
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300 overflow-x-hidden">
        <TheNavbar />
        <div class="relative bg-gradient-to-b from-white to-indigo-50 dark:from-gray-900 dark:to-gray-800 pt-10 pb-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <Tag value="Descubra" severity="info" class="mb-4" rounded />
                    <h2 class="text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Em alta na Ficção
                    </h2>
                    <p class="text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                        Explore os livros que estão movimentando o mundo literário hoje.
                    </p>
                </div>
                <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 md:gap-8">
                    <div v-for="i in 5" :key="i" class="flex flex-col gap-3">
                        <Skeleton width="100%" height="280px" borderRadius="16px" />
                        <Skeleton width="80%" height="1.2rem" />
                        <Skeleton width="50%" height="1rem" />
                    </div>
                </div>
                <div v-else class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 md:gap-8 perspective-1000">
                    <div v-for="(book, index) in trendingBooks" :key="book.id" @click="goToBook(book.id)"
                        class="group cursor-pointer flex flex-col text-left space-y-3 transition-all duration-500 hover:-translate-y-2"
                        :style="{ animationDelay: `${index * 100}ms` }">
                        <div
                            class="relative w-full aspect-[2/3] rounded-2xl overflow-hidden shadow-lg group-hover:shadow-2xl transition-all duration-300 bg-gray-200 dark:bg-gray-800">
                            <img v-if="book.image_url" :src="getCleanCoverUrl(book.image_url)" :alt="book.title"
                                class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                            <div v-else class="w-full h-full flex items-center justify-center"><i
                                    class="pi pi-image text-gray-400 text-3xl"></i></div>
                            <div
                                class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-[2px]">
                                <Button label="Ver Detalhes" size="small" rounded severity="white"
                                    class="!font-bold transform translate-y-4 group-hover:translate-y-0 transition-transform" />
                            </div>
                        </div>
                        <div>
                            <h3
                                class="font-bold text-gray-900 dark:text-white line-clamp-2 text-sm md:text-base leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                {{ book.title }}
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-1 font-medium">
                                {{ book.author }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="relative bg-indigo-50 dark:bg-gray-800 py-20 md:py-32 border-b border-indigo-100 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 dark:text-white mb-6 leading-tight">
                        Sua vida literária <br>
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">
                            finalmente organizada.
                        </span>
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                        Esqueça as planilhas complicadas. O MyPlot é seu assistente pessoal para rastrear progresso,
                        criar metas e montar a estante dos seus sonhos, de forma simples e visual.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Button label="Criar Conta Grátis" size="large" @click="router.push('/register')" raised
                            class="!font-bold !text-lg !px-8 !py-4" />
                        <Button label="Fazer Login" size="large" severity="secondary" @click="router.push('/login')"
                            class="!font-bold !text-lg !px-8 !py-4 bg-white dark:bg-gray-700" />
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900">
            <div class="py-24 border-b border-gray-100 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row items-center gap-16">
                        <div class="flex-1 space-y-6 text-center lg:text-left">
                            <div
                                class="inline-flex items-center justify-center p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-2xl mb-2">
                                <i class="pi pi-book text-2xl text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            <h2 class="text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                                Sua biblioteca,<br>visual e organizada.
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                                Separe o que você quer ler, o que está lendo e o que já finalizou. Com capas automáticas
                                e status personalizados, sua coleção nunca pareceu tão bonita.
                            </p>
                        </div>
                        <div class="flex-1 w-full relative group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                            </div>
                            <div
                                class="relative rounded-xl shadow-2xl overflow-hidden border-[6px] border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <img src="/screenshots/estante.png" alt="Minha Estante Desktop"
                                    class="w-full h-auto object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-24 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row-reverse items-center gap-16">
                        <div class="flex-1 space-y-6 text-center lg:text-left">
                            <div
                                class="inline-flex items-center justify-center p-3 bg-pink-100 dark:bg-pink-900/30 rounded-2xl mb-2">
                                <i class="pi pi-mobile text-2xl text-pink-600 dark:text-pink-400"></i>
                            </div>
                            <h2 class="text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                                Leve sua estante<br>no bolso.
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                                No ônibus, na fila ou na cama. O MyPlot é totalmente responsivo e oferece uma
                                experiência incrível no seu celular, onde quer que você esteja.
                            </p>
                        </div>
                        <div class="flex-1 flex justify-center gap-6 w-full">
                            <div
                                class="relative w-[240px] border-[8px] border-gray-800 dark:border-gray-700 rounded-[2.5rem] shadow-2xl overflow-hidden bg-gray-800 transform -rotate-2 hover:rotate-0 transition-all duration-500 aspect-[9/19]">
                                <div
                                    class="absolute top-0 inset-x-0 h-6 bg-gray-800 dark:bg-gray-700 rounded-b-2xl mx-auto w-24 z-20">
                                </div>
                                <img src="/screenshots/mobile_home.png"
                                    class="w-full h-full object-cover pt-4 bg-slate-50 dark:bg-gray-900">
                            </div>
                            <div
                                class="relative w-[240px] border-[8px] border-gray-800 dark:border-gray-700 rounded-[2.5rem] shadow-2xl overflow-hidden bg-gray-800 transform rotate-2 translate-y-8 hover:rotate-0 hover:translate-y-0 transition-all duration-500 aspect-[9/19] hidden md:block">
                                <div
                                    class="absolute top-0 inset-x-0 h-6 bg-gray-800 dark:bg-gray-700 rounded-b-2xl mx-auto w-24 z-20">
                                </div>
                                <img src="/screenshots/mobile_book.png"
                                    class="w-full h-full object-cover pt-4 bg-slate-50 dark:bg-gray-900">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col lg:flex-row items-center gap-16">
                        <div class="flex-1 space-y-6 text-center lg:text-left">
                            <div
                                class="inline-flex items-center justify-center p-3 bg-purple-100 dark:bg-purple-900/30 rounded-2xl mb-2">
                                <i class="pi pi-chart-bar text-2xl text-purple-600 dark:text-purple-400"></i>
                            </div>
                            <h2 class="text-4xl font-bold text-gray-900 dark:text-white leading-tight">
                                Metas e Estatísticas
                            </h2>
                            <p class="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                                Defina metas de leitura anuais e acompanhe seu progresso visualmente com gráficos
                                detalhados. Saiba exatamente o quanto você lê e mantenha-se motivado.
                            </p>
                        </div>
                        <div class="flex-1 w-full relative group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-purple-500 to-pink-500 rounded-xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                            </div>
                            <div
                                class="relative rounded-xl shadow-2xl overflow-hidden border-[6px] border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <img src="/screenshots/dashboard.png" alt="Dashboard Desktop"
                                    class="w-full h-auto object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative overflow-hidden py-24 bg-gray-900 text-white">
            <div class="absolute inset-0 bg-indigo-600 opacity-20"></div>
            <div class="max-w-4xl mx-auto text-center px-4 relative z-10">
                <h2 class="text-4xl md:text-5xl font-extrabold mb-8">
                    Comece sua jornada hoje.
                </h2>
                <Button label="Criar Minha Conta" severity="help" size="large" @click="router.push('/register')"
                    class="!font-bold !text-lg !px-12 !py-4 shadow-xl hover:scale-105 transition-transform" rounded />
            </div>
        </div>
        <footer class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <div
                    class="flex items-center justify-center mb-6 opacity-50 grayscale hover:opacity-100 hover:grayscale-0 transition-all duration-300">
                    <img src="../assets/logo_black.png" alt="My Plot Logo" class="h-8 w-auto block dark:hidden" />
                    <img src="../assets/logo_white.png" alt="My Plot Logo" class="h-8 w-auto hidden dark:block" />
                    <span class="ml-3 text-lg font-bold text-gray-600 dark:text-gray-300">MyPlot</span>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    &copy; {{ new Date().getFullYear() }} MyPlot.
                </p>
            </div>
        </footer>
    </div>
</template>