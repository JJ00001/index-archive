<script setup>
import { onMounted, ref, watch } from "vue";
import Chart from "primevue/chart";

const props = defineProps({
    data: {
        type: Object,
        required: true
    },
    modifyOptions: {
        type: Function,
        required: false
    }
});

const chartData = ref();
const chartOptions = ref();

const generateColor = (index, alpha = 0.2) => {
    const hue = (index * 137.508) % 360;
    return {
        solid: `hsl(${hue}, 30%, 50%)`,
        transparent: `hsla(${hue}, 70%, 50%, ${alpha})`
    };
};

const setChartData = () => {
    return {
        labels: props.data.labels,
        datasets: Object.keys(props.data.datasets).map((dataset, index) => {
            const color = generateColor(index);

            return {
                label: dataset,
                data: props.data.datasets[dataset],
                fill: true,
                backgroundColor: color.transparent,
                borderColor: color.solid,
                tension: 0.5
            };
        })
    };
};

const setChartOptions = () => {
    const documentStyle = getComputedStyle(document.documentElement);
    const textColor = documentStyle.getPropertyValue('--p-text-color');
    const textColorSecondary = documentStyle.getPropertyValue('--p-text-muted-color');
    const surfaceBorder = documentStyle.getPropertyValue('--p-content-border-color');

    let options = {
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
                    color: textColorSecondary
                },
                grid: {
                    color: 'transparent'
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

    if (props.modifyOptions) {
        options = props.modifyOptions(options);
    }

    return options;
};

onMounted(() => {
    chartData.value = setChartData();
    chartOptions.value = setChartOptions();
});

watch(() => props.data, () => {
    chartData.value = setChartData();
});
</script>

<template>
    <chart :data="chartData" :options="chartOptions" class="h-[20rem]" type="line"/>
</template>
