<script setup>
import BaseLineChart from "@/Components/Charts/BaseLineChart.vue";

const props = defineProps({
    data: {
        type: Object,
        required: true
    }
});

const modifyOptions = (options) => {
    options.scales.y.stacked = true;
    options.scales.y.min = 0;
    options.scales.y.max = 100;

    return options;
};
</script>

<template>
    <base-line-chart :data="data" :modifyOptions="modifyOptions"/>
</template>
