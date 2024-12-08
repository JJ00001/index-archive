<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import Card from "primevue/card";
import WeightChart from "@/Components/WeightChart.vue";
import CompanyIndexTable from "@/Components/CompanyIndexTable.vue";

const props = defineProps({
    sector: {
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
            <h1 class="text-4xl font-bold">{{ sector.name }}</h1>
            <card>
                <template #content>
                    <weight-chart :data="weightHistory"/>
                </template>
            </card>
            <card>
                <template #content>
                    <company-index-table :companies="companies" :next-page="nextCompaniesPage"/>
                </template>
            </card>
        </div>
    </layout-main>
</template>
