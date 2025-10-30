<script setup>
import CompanyInfoHeader from '@/Components/CompanyInfoHeader.vue'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'

defineProps({
    indexHolding: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <div class="space-y-6">
        <CompanyInfoHeader :company="indexHolding.company" />
        <Card>
            <CardHeader>
                <CardTitle class="text-2xl font-bold">{{ $t('weight') }}</CardTitle>
            </CardHeader>
            <CardContent>
                <weight-chart :data="indexHolding.weight_history"
                              class="h-60" />
            </CardContent>
        </Card>
    </div>
</template>