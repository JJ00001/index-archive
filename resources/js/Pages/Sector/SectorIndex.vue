<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
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
            <Card>
                <CardContent>
                    <sector-index-table :sector-data="sectorData"/>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">Gewichtungs-Vergleich</CardTitle>
                </CardHeader>
                <CardContent>
                  <stacked-line-chart :data="multipleWeightHistoryData"
                                      class="h-160" />
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>
