<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { useI18n } from 'vue-i18n'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import SectorIndexTable from '@/Components/SectorIndexTable.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'

const props = defineProps({
    index: {
        type: Object,
        required: true,
    },
    stats: {
        type: Array,
        required: true,
    },
    companies: {
        type: Object,
        required: true,
    },
    sectors: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()

const breadcrumbItems = [
    {
        label: t('index.name'),
        route: '/indices',
    },
    {
        label: props.index.name,
        route: null,
    },
]

const companiesCount = props.companies.data.length
</script>

<template>
    <layout-main>
        <breadcrumbs :items="breadcrumbItems" />

        <div class="space-y-10">
            <h1 class="text-3xl font-bold">{{ index.name }}</h1>
            <StatCardGroup :stats="stats" />
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('index.holding', 2) + ' (Top ' + companiesCount + ')' }}</CardTitle>
                    <CardDescription>
                        Companies currently held in the {{ index.name }} index
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <company-index-table :companies="companies" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('sector.name', 2) }}</CardTitle>
                    <CardDescription>
                        Sector allocation for the {{ index.name }} index
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <sector-index-table :sector-data="sectors.data" />
                </CardContent>
            </Card>
        </div>
    </layout-main>
</template>