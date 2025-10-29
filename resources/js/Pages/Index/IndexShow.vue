<script setup>
import { ref } from 'vue'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { useI18n } from 'vue-i18n'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import SectorIndexTable from '@/Components/SectorIndexTable.vue'
import CountryIndexTable from '@/Components/CountryIndexTable.vue'
import IndexActivityLog from '@/Components/IndexActivityLog.vue'
import HoldingDataShow from '@/Pages/HoldingData/HoldingDataShow.vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { Dialog, DialogContent } from '@/Components/ui/dialog'

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

const showIndexHoldingDialog = ref(false)
const selectedIndexHolding = ref(null)
const isLoading = ref(false)

const handleIndexHoldingRowClick = async (company) => {
    isLoading.value = true
    try {
        const response = await fetch(route('api.index-holdings.show', { indexHolding: company.index_holding_id }))
        const data = await response.json()
        selectedIndexHolding.value = data.props.indexHolding
        showIndexHoldingDialog.value = true
    } catch (error) {
        console.error('Error fetching IndexHolding details:', error)
    } finally {
        isLoading.value = false
    }
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
                    <company-index-table :companies="companies"
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
                    <sector-index-table :sector-data="sectors.data" />
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
                    <country-index-table :country-data="countries.data" />
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
        <Dialog :open="showIndexHoldingDialog"
                @update:open="showIndexHoldingDialog = $event">
            <DialogContent class="max-w-5xl max-h-[80vh] overflow-y-auto">
                <HoldingDataShow v-if="selectedIndexHolding"
                                 :indexHolding="selectedIndexHolding" />
            </DialogContent>
        </Dialog>
    </layout-main>
</template>
