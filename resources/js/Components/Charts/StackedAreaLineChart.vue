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
    options.scales.y.stacked = true;
    options.scales.y.min = 0;
    options.scales.y.ticks.callback = function (value) {
        return n(value, {style: 'percent', maximumFractionDigits: 0});
    };
    options.scales.x.ticks.callback = function (value, index) {
        // Show every 12th label
        return index % 12 === 0 ? d(new Date(this.getLabelForValue(value)), 'short') : '';
    }

    return options;
};
</script>

<template>
    <base-line-chart :data="data" :modifyOptions="modifyOptions"/>
</template>
