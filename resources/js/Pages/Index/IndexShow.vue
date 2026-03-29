<script lang="ts"
        setup>
import {computed, ref} from 'vue'
import {Head, router} from '@inertiajs/vue3'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import {useI18n} from 'vue-i18n'
import SectorIndexTable from '@/Components/tables/SectorIndexTable.vue'
import CountryIndexTable from '@/Components/tables/CountryIndexTable.vue'
import IndexActivityLog from '@/Components/IndexActivityLog.vue'
import IndexHoldingDialog from '@/Components/Dialogs/IndexHoldingDialog.vue'
import IndexSectorDialog from '@/Components/Dialogs/IndexSectorDialog.vue'
import IndexCountryDialog from '@/Components/Dialogs/IndexCountryDialog.vue'
import {Card, CardContent, CardDescription, CardHeader, CardTitle} from '@/Components/ui/card'
import IndexHoldingTable from '@/Components/tables/IndexHoldingTable.vue'
import type {Index} from "@/interfaces/Index.ts";
import type {Paginated} from "@/interfaces/Paginated.ts";
import type {Company} from "@/interfaces/Company.ts";
import type {IndexHolding} from "@/interfaces/IndexHolding.ts";
import type {Sector} from "@/interfaces/Sector.ts";
import type {Country} from "@/interfaces/Country.ts";
import type {IndexActivity} from "@/interfaces/IndexActivity.ts";
import type {StatCard} from "@/interfaces/StatCard.ts";

const props = defineProps<{
    index: Index
    stats: StatCard[]
    companies: Paginated<Company>
    sectors: Sector[]
    countries: Country[]
    activities: IndexActivity[]
    sort: {}
}>()

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

const companiesCount = props.index.index_holdings_count

const holdingDialogRef = ref<InstanceType<typeof IndexHoldingDialog>>()
const sectorDialogRef = ref<InstanceType<typeof IndexSectorDialog>>()
const countryDialogRef = ref<InstanceType<typeof IndexCountryDialog>>()

const handleIndexHoldingRowClick = (holding: IndexHolding) => {
    holdingDialogRef.value?.open(holding.index_holding_id)
}

const handleSectorRowClick = (sector: Sector) => {
    sectorDialogRef.value?.open(sector.id)
}

const handleCountryRowClick = (country: Country) => {
    countryDialogRef.value?.open(country.id)
}

const visitIndexShow = (params = {}, options = {}) => {
    router.get(
        route('indices.show', props.index.id),
        params,
        {
            reset: ['companies'],
            preserveState: true,
            preserveScroll: true,
            ...options,
        },
    )
}

</script>

<template>
    <layout-main>
        <Head :title="index.name" />
        <breadcrumbs :items="breadcrumbItems" />

        <div class="space-y-10">
            <h1 class="text-3xl font-semibold">{{ index.name }}</h1>
            <StatCardGroup :stats="stats" />
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('indexHolding.name', 2) + ' (' + companiesCount + ')' }}</CardTitle>
                    <CardDescription>
                        Companies currently held in the {{ index.name }} index
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <index-holding-table
                        :companies="companies"
                        :on-request-sort="visitIndexShow"
                        :on-row-click="handleIndexHoldingRowClick"
                        :sort="sort"
                    />
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
                                        :sector-data="sectors" />
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
                    <country-index-table :country-data="countries"
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
