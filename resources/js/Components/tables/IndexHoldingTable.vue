<script setup>
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
    columnHelper.accessor('weight', {
        id: 'weight',
        header: () => t('weight'),
        cell: ({ getValue }) => n(getValue(), 'percentFine'),
        meta: {
            headerClass: 'w-1/12 text-right',
            cellClass: 'w-1/12 text-right',
        },
    }),
    columnHelper.accessor(row => row.company.name, {
        id: 'company',
        header: () => t('company.name'),
        cell: ({ row }) => h(CompanyDisplay, { company: row.original.company }),
        meta: {
            headerClass: 'pl-10 w-11/12',
            cellClass: 'pl-10 w-11/12',
        },
    }),
]

const handleRowClick = (holding) => {
    props.onRowClick(holding)
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
