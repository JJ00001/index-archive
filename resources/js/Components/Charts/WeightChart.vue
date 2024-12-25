<script setup>
import Chart from "primevue/chart";
import { onMounted, ref } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const {n, d} = useI18n();

const dates = ref(props.data['dates']);
const weights = ref(props.data['weights']);
const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: dates,
        datasets: [
            {
                label: "Gewichtung (%) im Index",
                data: weights,
                fill: false,
                borderColor: documentStyle.getPropertyValue('--p-cyan-500'),
                tension: 0.5
            }
        ]
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    return {
        maintainAspectRatio: false,
        aspectRatio: 0.6,
        plugins: {
            legend: {
                labels: {
                    color: textColor
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: textColorSecondary,
                    callback: function (value, index) {
                        // Show every 12th label
                        return index % 12 === 0 ? d(new Date(this.getLabelForValue(value)), 'short') : '';
                    }
                },
                grid: {
                    color: 'transparent'
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary,
                    callback: function (value) {
                        return n(value, 'percent');
                    }
                },
                grid: {
                    color: surfaceBorder
                },
                min: 0,
            }
        }
    };
}

onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});
</script>

<template>
    <chart :data="chartData" :options="chartOptions" class="h-[20rem]" type="line"/>
</template>
