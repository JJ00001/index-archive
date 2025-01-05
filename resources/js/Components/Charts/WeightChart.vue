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
    options.scales.x.ticks.callback = function (value, index) {
        return index % 12 === 0 ? d(new Date(this.getLabelForValue(value)), "short") : "";
    };
    options.scales.y.min = 0;
    options.scales.y.ticks.callback = function (value) {
        return n(value, 'percent');
    };

    return options;
};
</script>

<template>
    <base-line-chart :data="data" :modifyOptions="modifyOptions"/>
</template>
