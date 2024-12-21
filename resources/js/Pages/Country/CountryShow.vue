<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import Card from "primevue/card";
import WeightChart from "@/Components/Charts/WeightChart.vue";
import CompanyIndexTable from "@/Components/CompanyIndexTable.vue";

const props = defineProps({
    country: {
        type: Object,
        required: true
    },
    weightHistory: {
        type: Object,
        required: true
    },
    companies: {
        type: Object,
        required: true
    },
    nextCompaniesPage: {
        type: Number,
        required: true
    }
});
</script>

<template>
    <layout-main>
        <div class="space-y-10">
            <h1 class="text-4xl font-bold">{{ country.name }}</h1>
            <card>
                <template #title>
                    <h2 class="text-2xl font-bold">Gewichtung</h2>
                </template>
                <template #content>
                    <weight-chart :data="weightHistory"/>
                </template>
            </card>
            <card>
                <template #title>
                    <h2 class="text-2xl font-bold">Unternehmen</h2>
                </template>
                <template #content>
                    <company-index-table :companies="companies" :next-page="nextCompaniesPage"/>
                </template>
            </card>
        </div>
    </layout-main>
</template>
