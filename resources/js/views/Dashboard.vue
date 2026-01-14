<script setup>
import { ref, onMounted } from 'vue';
import http from '../services/http';
import Chart from 'primevue/chart';
import TheNavbar from '../components/TheNavbar.vue';

const stats = ref({
    total: 0,
    read: 0,
    reading: 0,
    planning: 0
});

// Gráfico de Donut
const statusChartData = ref(null);
const monthlyChartData = ref(null);
const doughnutOptions = ref({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
            labels: { 
                color: '#6b7280',
                usePointStyle: true,
                padding: 15,
                boxWidth: 100,
                boxPadding: 10,
                font: { size: 13 }
            }
        }
    },
    layout: {
        padding: 20
    },
    scales: {
        x: { display: false },
        y: { display: false }
    }
});

// Gráfico de Barras
const barOptions = ref({
    responsive: true,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
                color: '#6b7280'
            },
            grid: {
                color: '#c9c9c936'
            }
        },
        x: {
            ticks: { color: '#6b7280' },
            grid: { display: false }
        }
    }
});

const fetchDashboardData = async () => {
    try {
        const response = await http.get('/dashboard');
        const data = response.data;

        stats.value = data.counts;

        statusChartData.value = {
            labels: ['Lidos', 'Lendo', 'Quero Ler'],
            datasets: [{
                data: [stats.value.read, stats.value.reading, stats.value.planning],
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                hoverBackgroundColor: ['#059669', '#2563eb', '#d97706']
            }],
        };

        console.log(data.monthly_reads);
        const counts = data.monthly_reads.map(item => item.count);
        const months = data.monthly_reads.map(item => item.label);

        monthlyChartData.value = {
            labels: months,
            datasets: [{
                label: 'Livros Finalizados',
                data: counts,
                backgroundColor: '#6366f1',
                borderRadius: 6
            }]
        };

    } catch (error) {
        console.error("Erro ao carregar dashboard:", error);
    }
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <TheNavbar />
        <main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">
                Meu Painel
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-indigo-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium">Total de Livros</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.total }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-emerald-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium">Livros Lidos</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.read }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium">Lendo Agora</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.reading }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border-l-4 border-amber-500">
                    <div class="text-gray-500 dark:text-gray-400 text-sm font-medium">Quero Ler</div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ stats.planning }}</div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribuição da Estante</h3>
                    <div class="h-64 flex justify-center">
                        <Chart v-if="statusChartData" type="doughnut" :data="statusChartData" :options="doughnutOptions"class="w-full h-full" />
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Leituras nos últimos 6 meses
                    </h3>
                    <div class="h-64">
                        <Chart v-if="monthlyChartData" type="bar" :data="monthlyChartData" :options="barOptions"class="w-full h-full" />
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>