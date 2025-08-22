<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
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
            <Card>
                <CardContent>
                    <company-index-table :companies="companies" :next-page="nextPage"/>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">Gewichtungs-Vergleich (Top 10)</CardTitle>
                </CardHeader>
                <CardContent>
                  <stacked-line-chart :data="multipleWeightHistoryData"
                                      class="h-160" />
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>
