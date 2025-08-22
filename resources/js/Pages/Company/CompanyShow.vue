<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyInfoHeader from '@/Components/CompanyInfoHeader.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'

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

const breadcrumbItems = [
    {
        label: 'Unternehmen',
        route: '/companies'
    },
    {
        label: props.company.name,
        route: null
    },
];
</script>

<template>
    <layout-main>
        <breadcrumbs :items="breadcrumbItems"/>
        <div class="space-y-10">
            <CompanyInfoHeader :company="company"/>
            <Card>
                <CardHeader>
                    <CardTitle class="text-2xl font-bold">Gewichtung</CardTitle>
                </CardHeader>
                <CardContent>
                    <weight-chart :data="weightHistory"/>
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>
