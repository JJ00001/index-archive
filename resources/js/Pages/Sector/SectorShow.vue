<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import { useI18n } from 'vue-i18n'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

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

const { n, t } = useI18n()

const sectorStats = [
    {
        title: t('company.count'),
        value: props.sector.companies_count,
    },
    {
        title: t('weight'),
        value: n(Number(props.sector.weight), 'percent'),
    }
]

const breadcrumbItems = [
    {
        label: t('sector.name', 2),
        route: '/sectors'
    },
    {
        label: props.sector.name,
        route: null
    },
];
</script>

<template>
    <layout-main>
        <breadcrumbs :items="breadcrumbItems"/>
        <div class="space-y-10">
            <h1 class="text-4xl font-bold">{{ sector.name }}</h1>
            <stat-card-group :stats="sectorStats"/>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">{{ $t('weight') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <weight-chart :data="weightHistory"
                                  class="h-60" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">{{ $t('company.name', 2) }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <company-index-table :companies="companies" :next-page="nextCompaniesPage"/>
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>
