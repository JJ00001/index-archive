<script setup>
import { computed } from 'vue'
import LineChart from '@/Components/ui/chart-line/LineChart.vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
})

const { n, d } = useI18n()

const chartData = computed(() => {
    if (!props.data?.labels || !props.data?.datasets?.Gewichtung) {
        return []
    }

    return props.data.labels.map((label, index) => ({
        date: label,
        Gewichtung: parseFloat(props.data.datasets.Gewichtung[index]),
    }))
})

const categories = ['Gewichtung']

const xFormatter = (value) => {
    const dataPoint = chartData.value[value]
    if (!dataPoint) {
        return ''
    }
    const date = new Date(dataPoint.date)
    return d(date, 'short')
}

const yFormatter = (value) => {
    return n(value, 'percent')
}
</script>

<template>
    <LineChart
        :categories="categories"
        :data="chartData"
        :show-legend="false"
        :x-formatter="xFormatter"
        :y-formatter="yFormatter"
        index="date"
    />
</template>