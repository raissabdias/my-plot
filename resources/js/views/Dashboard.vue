<script setup>
import { ref, onMounted } from 'vue';
import http from '@/services/http';
import TheNavbar from '@/components/TheNavbar.vue';

import Chart from 'primevue/chart';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import Button from 'primevue/button';
import ProgressBar from 'primevue/progressbar';
import Skeleton from 'primevue/skeleton';

const loading = ref(true);
const stats = ref({ total: 0, read: 0, reading: 0, planning: 0 });
const goal = ref(null);
const showGoalDialog = ref(false); // Modal
const newTarget = ref(12);

// Configuração dos Gráficos
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

// Dados para os gráficos
const fetchDashboard = async () => {
    try {
        const { data } = await http.get('/dashboard');

        stats.value = data.counts;
        goal.value = data.goal;
        newTarget.value = goal.value ? goal.value.target : 12;
        setupCharts(data.counts, data.monthly_reads);
    } catch (error) {
        console.error("Erro ao carregar dashboard:", error);
    } finally {
        loading.value = false;
    }
};

// Salvar meta de leitura
const saveGoal = async () => {
    try {
        await http.post('/reading-goal', { target: newTarget.value });
        showGoalDialog.value = false;
        fetchDashboard();
    } catch (error) {
        console.error("Erro ao salvar meta", error);
    }
};

// Helpers de Gráfico
const setupCharts = (counts, monthlyReads) => {
    // Gráfico de Pizza (Status)
    statusChartData.value = {
        labels: ['Lidos', 'Lendo', 'Quero ler'],
        datasets: [{
            data: [counts.read, counts.reading, counts.planning],
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
            hoverBackgroundColor: ['#059669', '#2563eb', '#d97706']
        }]
    };

    // Gráfico de Barras (Mensal)
    monthlyChartData.value = {
        labels: monthlyReads.map(m => m.label),
        datasets: [{
            label: 'Livros Lidos',
            data: monthlyReads.map(m => m.count),
            backgroundColor: '#6366f1',
            borderRadius: 6
        }]
    };
};

onMounted(() => {
    fetchDashboard();
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-gray-900 transition-colors duration-300">
        <TheNavbar />
        <main class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div v-if="loading">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="i in 4" :key="i" class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-gray-200 dark:border-gray-700">
                            <Skeleton width="3rem" height="0.8rem" class="mb-2"></Skeleton> 
                            <Skeleton width="50%" height="2rem"></Skeleton>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm h-full flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-4">
                            <Skeleton width="8rem" height="1.5rem"></Skeleton>
                            <Skeleton shape="circle" size="2rem"></Skeleton>
                        </div>
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-center">
                                <Skeleton width="60%" height="3rem"></Skeleton>
                            </div>
                            <Skeleton width="100%" height="1rem" borderRadius="10px"></Skeleton>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <Skeleton width="10rem" height="1.5rem" class="mb-6"></Skeleton> <div class="h-64 flex justify-center items-center">
                            <Skeleton shape="circle" size="12rem"></Skeleton> </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <Skeleton width="12rem" height="1.5rem" class="mb-6"></Skeleton> <div class="h-64 flex items-end gap-2">
                            <Skeleton width="100%" height="40%"></Skeleton>
                            <Skeleton width="100%" height="80%"></Skeleton>
                            <Skeleton width="100%" height="60%"></Skeleton>
                            <Skeleton width="100%" height="90%"></Skeleton>
                            <Skeleton width="100%" height="50%"></Skeleton>
                            <Skeleton width="100%" height="70%"></Skeleton>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-indigo-500">
                            <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Total</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.total }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-emerald-500">
                            <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Lidos</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.read }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-blue-500">
                            <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Lendo</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.reading }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border-l-4 border-amber-500">
                            <div class="text-gray-500 dark:text-gray-400 text-xs font-medium uppercase tracking-wider">Quero Ler</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ stats.planning }}</div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm h-full flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                Meta {{ new Date().getFullYear() }}
                            </h3>
                            <Button icon="pi pi-pencil" text rounded size="small" @click="showGoalDialog=true" class="p-button-secondary -mr-2" />
                        </div>
                        <div v-if="!goal" class="text-center py-2">
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Quantos livros vai ler este ano?</p>
                            <Button label="Definir Meta" size="small" icon="pi pi-flag" @click="showGoalDialog = true" />
                        </div>
                        <div v-else class="flex flex-col gap-4">
                            <div class="flex items-baseline justify-center gap-2">
                                <span class="text-5xl font-bold text-indigo-600 dark:text-indigo-400">{{ goal.read }}</span>
                                <span class="text-gray-400 text-xl">/</span>
                                <span class="text-2xl font-semibold text-gray-600 dark:text-gray-300">{{ goal.target }}</span>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-xs mb-1">
                                    <span class="text-gray-500">Progresso</span>
                                    <span :class="goal.status === 'on_track' ? 'text-emerald-500' : 'text-amber-500'" class="font-bold">
                                        {{ goal.percentage }}% ({{ goal.status === 'on_track' ? 'No ritmo' : 'Atrasado' }})
                                    </span>
                                </div>
                                <ProgressBar :value="goal.percentage" :showValue="false" style="height: 10px; background: #f3f4f6;" 
                                    :pt="{ value: { class: goal.status === 'on_track' ? 'bg-emerald-500' : 'bg-amber-500' } }" 
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Distribuição</h3>
                        <div class="h-64 flex justify-center">
                            <Chart v-if="statusChartData" type="doughnut" :data="statusChartData" :options="doughnutOptions" class="w-full h-full" />
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                            Últimos 6 meses
                        </h3>
                        <div class="h-64">
                            <Chart v-if="monthlyChartData" type="bar" :data="monthlyChartData" :options="barOptions" class="w-full h-full" />
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <Dialog v-model:visible="showGoalDialog" modal header="Minha Meta Anual" :style="{ width: '400px' }" class="p-fluid">
            <div class="p-4">
                <label for="target" class="block text-gray-700 dark:text-gray-200 mb-2">Quantos livros você quer ler em {{ new Date().getFullYear() }}?</label>
                <InputNumber v-model="newTarget" inputId="target" :min="1" :max="365" showButtons buttonLayout="horizontal" class="w-full" />
            </div>
            <template #footer>
                <Button label="Cancelar" text @click="showGoalDialog = false" class="p-button-secondary" />
                <Button label="Salvar Meta" icon="pi pi-check" @click="saveGoal" autofocus />
            </template>
        </Dialog>
    </div>
</template>