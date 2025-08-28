<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyInfoHeader from '@/Components/CompanyInfoHeader.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { useI18n } from 'vue-i18n'

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

const { t } = useI18n()

const breadcrumbItems = [
    {
        label: t('company.name'),
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
                    <CardTitle class="text-2xl font-bold">{{ $t('weight') }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <weight-chart :data="weightHistory"
                                  class="h-60" />
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>
