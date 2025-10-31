<script setup>
import { ref } from 'vue'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { useI18n } from 'vue-i18n'
import SectorIndexTable from '@/Components/SectorIndexTable.vue'
import CountryIndexTable from '@/Components/CountryIndexTable.vue'
import IndexActivityLog from '@/Components/IndexActivityLog.vue'
import IndexHoldingDialog from '@/Components/Dialogs/IndexHoldingDialog.vue'
import IndexSectorDialog from '@/Components/Dialogs/IndexSectorDialog.vue'
import IndexCountryDialog from '@/Components/Dialogs/IndexCountryDialog.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import IndexHoldingTable from '@/Components/IndexHoldingTable.vue'

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
    countries: {
        type: Object,
        required: true,
    },
    activities: {
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

const holdingDialogRef = ref(null)
const sectorDialogRef = ref(null)
const countryDialogRef = ref(null)

const handleIndexHoldingRowClick = (company) => {
    holdingDialogRef.value?.open(company.index_holding_id)
}

const handleSectorRowClick = (sector) => {
    sectorDialogRef.value?.open(sector.id)
}

const handleCountryRowClick = (country) => {
    countryDialogRef.value?.open(country.id)
}

</script>

<template>
    <layout-main>
        <breadcrumbs :items="breadcrumbItems" />

        <div class="space-y-10">
            <h1 class="text-3xl font-bold">{{ index.name }}</h1>
            <StatCardGroup :stats="stats" />
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('indexHolding.name', 2) + ' (Top ' + companiesCount + ')' }}</CardTitle>
                    <CardDescription>
                        Companies currently held in the {{ index.name }} index
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <index-holding-table :companies="companies"
                                         :on-row-click="handleIndexHoldingRowClick" />
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
                    <sector-index-table :on-row-click="handleSectorRowClick"
                                        :sector-data="sectors.data" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('country.name', 2) }}</CardTitle>
                    <CardDescription>
                        Country allocation for the {{ index.name }} index
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <country-index-table :country-data="countries.data"
                                         :on-row-click="handleCountryRowClick" />
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>Recent Activity</CardTitle>
                    <CardDescription>
                        Companies added to or removed from the {{ index.name }} index
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <index-activity-log :activities="activities" />
                </CardContent>
            </Card>
        </div>
        <IndexHoldingDialog ref="holdingDialogRef" />

        <IndexSectorDialog ref="sectorDialogRef"
                           :index-id="index.id" />

        <IndexCountryDialog ref="countryDialogRef"
                            :index-id="index.id" />
    </layout-main>
</template>
