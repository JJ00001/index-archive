<script setup>
import { router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h } from 'vue'
import { useI18n } from 'vue-i18n'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import DataTable from '@/Components/ui/data-table/DataTable.vue'

const props = defineProps({
    companies: {
        type: Object,
        required: true,
    },
    onRowClick: {
        type: Function,
        default: null,
    },
})

const { t, n } = useI18n()
const columnHelper = createColumnHelper()

const columns = [
    columnHelper.accessor('rank', {
        id: 'rank',
        header: () => t('rank'),
        cell: ({ getValue }) => getValue(),
        meta: {
            headerClass: 'w-1/12',
            cellClass: 'w-1/12 font-mono',
        },
    }),
    columnHelper.accessor('name', {
        id: 'name',
        header: () => t('company.name'),
        cell: ({ row }) => h(CompanyDisplay, { company: row.original }),
        meta: {
            headerClass: 'w-10/12',
            cellClass: 'w-10/12',
        },
    }),
    columnHelper.accessor('weight', {
        id: 'weight',
        header: () => t('weight'),
        cell: ({ getValue }) => n(getValue(), 'percentFine'),
        meta: {
            headerClass: 'w-1/12 text-right',
            cellClass: 'w-1/12 text-right',
        },
    }),
]

const handleRowClick = (company) => {
    if (props.onRowClick) {
        props.onRowClick(company)
    } else {
        router.get(route('companies.show', company.id))
    }
}
</script>

<template>
    <DataTable
        :columns="columns"
        :data="companies"
        :on-row-click="handleRowClick"
        body-id="index-holding-table-body"
        infinite-scroll-key="companies"
        remember-scroll-key="IndexHoldingTable"
    />
</template>
