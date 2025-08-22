<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import { Card } from 'primevue'
import StackedLineChart from '@/Components/Charts/StackedLineChart.vue'
import { ref } from 'vue'

const props = defineProps({
    companies: {
        type: Object,
        required: true
    },
    nextPage: {
        type: Number,
        required: true
    },
    multipleWeightHistory: {
        type: Object,
        required: true
    },
});

const multipleWeightHistoryData = ref({
    labels: props.multipleWeightHistory.labels,
    datasets: props.multipleWeightHistory.datasets
});
</script>

<template>
    <layout-main>
        <div class="space-y-10">
            <card>
                <template #content>
                    <company-index-table :companies="companies" :next-page="nextPage"/>
                </template>
            </card>
            <card>
                <template #title>
                    <h2 class="text-2xl font-bold">Gewichtungs-Vergleich (Top 10)</h2>
                </template>
                <template #content>
                  <stacked-line-chart :data="multipleWeightHistoryData"
                                      class="h-160" />
                </template>
            </card>
        </div>
    </layout-main>
</template>
