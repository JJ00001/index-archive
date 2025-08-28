<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import { useI18n } from 'vue-i18n'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

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

const {n} = useI18n();

const countryStats = [
    {
        title: 'Anzahl Unternehmen',
        value: props.country.companies_count,
    },
    {
        title: 'Gewichtung',
        value: n(Number(props.country.weight), 'percent'),
    }
]

const breadcrumbItems = [
    {
        label: 'Länder',
        route: '/countries'
    },
    {
        label: props.country.name,
        route: null
    },
];
</script>

<template>
    <layout-main>
        <breadcrumbs :items="breadcrumbItems"/>
        <div class="space-y-10">
            <h1 class="text-4xl font-bold">{{ country.name }}</h1>
            <stat-card-group :stats="countryStats"/>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">Gewichtung</CardTitle>
                </CardHeader>
                <CardContent>
                    <weight-chart :data="weightHistory"
                                  class="h-60" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">Unternehmen</CardTitle>
                </CardHeader>
                <CardContent>
                    <company-index-table :companies="companies" :next-page="nextCompaniesPage"/>
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>
