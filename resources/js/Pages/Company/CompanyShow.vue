<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import CompanyInfoHeader from "@/Components/CompanyInfoHeader.vue";
import Card from "primevue/card";
import WeightChart from "@/Components/WeightChart.vue";

const props = defineProps({
    company: {
        type: Object,
        required: true
    },
    weightHistory: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <layout-main>
        <div class="space-y-10">
            <CompanyInfoHeader :company="company"/>
            <card>
                <template #title>
                    <h2 class="text-2xl font-bold">Gewichtung</h2>
                </template>
                <template #content>
                    <weight-chart :data="weightHistory"/>
                </template>
            </card>
        </div>
    </layout-main>
</template>
