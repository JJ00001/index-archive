<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import { Card } from 'primevue'
import SectorIndexTable from '@/Components/SectorIndexTable.vue'
import { ref } from 'vue'
import StackedLineChart from '@/Components/Charts/StackedLineChart.vue'

const props = defineProps({
    sectors: {
        type: Object,
        required: true
    },
    multipleWeightHistory: {
        type: Object,
        required: true
    },
})

const sectorData = ref([...props.sectors]);

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
                    <sector-index-table :sector-data="sectorData"/>
                </template>
            </card>
            <card>
                <template #title>
                    <h2 class="text-2xl font-bold">Gewichtungs-Vergleich</h2>
                </template>
                <template #content>
                  <stacked-line-chart :data="multipleWeightHistoryData"
                                      class="h-160" />
                </template>
            </card>
        </div>
    </layout-main>
</template>
