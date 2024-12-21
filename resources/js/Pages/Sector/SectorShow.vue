<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import Card from "primevue/card";
import WeightChart from "@/Components/Charts/WeightChart.vue";
import CompanyIndexTable from "@/Components/CompanyIndexTable.vue";
import StatCardGroup from "@/Components/StatCardGroup.vue";
import { useI18n } from "vue-i18n";

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

const {n} = useI18n();

const sectorStats = [
    {
        title: 'Anzahl Unternehmen',
        value: props.sector.companies_count,
    },
    {
        title: 'Gewichtung',
        value: n(Number(props.sector.weight), 'percent'),
    }
]
</script>

<template>
    <layout-main>
        <div class="space-y-10">
            <h1 class="text-4xl font-bold">{{ sector.name }}</h1>
            <stat-card-group :stats="sectorStats"/>
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
