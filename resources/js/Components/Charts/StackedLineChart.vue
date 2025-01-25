<script setup>
import BaseLineChart from "@/Components/Charts/BaseLineChart.vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const {n, d} = useI18n();

const modifyOptions = (options) => {
    options.scales.y.stacked = false;
    options.scales.y.min = 0;
    options.scales.y.ticks.callback = function (value) {
        return n(value, {style: 'percent', maximumFractionDigits: 0});
    };
    options.scales.x.ticks.callback = function (value, index) {
        // Show every 12th label
        return index % 12 === 0 ? d(new Date(this.getLabelForValue(value)), 'short') : '';
    }
    options.plugins.tooltip = {
        ...options.plugins.tooltip,
        callbacks: {
            label: (context) => {
                const label = context.dataset.label;
                const valueFormatted = n(context.parsed.y, 'percent');

                return `${label}: ${valueFormatted}`;
            },
            title: (context) => {
                const rawDate = context[0].label;

                return d(new Date(rawDate), 'short');
            },
        },
    };

    return options;
};

const modifyChartData = (chartData) => {
    return {
        ...chartData,
        datasets: chartData.datasets.map(dataset => ({
            ...dataset,
            fill: false
        }))
    };
};
</script>

<template>
    <base-line-chart :data="data" :modify-chart-data="modifyChartData" :modify-options="modifyOptions"/>
</template>
