<script setup>
import Chart from "primevue/chart";
import { onMounted, ref } from "vue";

const props = defineProps({
    company: {
        type: Object,
        required: true
    },
    weightHistory: {
        type: Array,
        required: true
    }
});

onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});

const chartData = ref();
const chartOptions = ref();

const setChartData = () => {
    const documentStyle = getComputedStyle(document.documentElement);

    return {
        labels: props.weightHistory['dates'],
        datasets: [
            {
                label: "Anteil vom MSCI World Index",
                data: props.weightHistory['weights'],
                fill: false,
                borderColor: documentStyle.getPropertyValue('--p-cyan-500'),
                tension: 0
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
                        // Show every 6th label
                        return index % 12 === 0 ? this.getLabelForValue(value) : '';
                    }
                },
                grid: {
                    color: surfaceBorder
                }
            },
            y: {
                ticks: {
                    color: textColorSecondary
                },
                grid: {
                    color: surfaceBorder
                }
            }
        }
    };
}
</script>

<template>
    <chart type="line" :data="chartData" :options="chartOptions" class="h-[30rem]"/>
</template>
