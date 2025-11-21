<script setup>
import { createColumnHelper } from '@tanstack/vue-table'
import { computed, h } from 'vue'
import { useI18n } from 'vue-i18n'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import DataTable from '@/Components/ui/data-table/DataTable.vue'
import DataTableColumnHeader from '@/Components/ui/data-table/DataTableColumnHeader.vue'
import useDataTableSorting from '@/composables/useDataTableSorting'

const props = defineProps({
    companies: {
        type: Object,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    onRowClick: {
        type: Function,
        default: null,
    },
    // Sorting
    sort: {
        type: Object,
        required: true,
    },
    onRequestSort: {
        type: Function,
        required: true,
    },
})

const { t, n } = useI18n()
const columnHelper = createColumnHelper()

const sort = computed(() => props.sort)
const { sorting, handleSortingChange } = useDataTableSorting(sort, props.onRequestSort)

const columns = [
    columnHelper.accessor('weight', {
        id: 'weight',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: t('weight'),
            }),
        cell: ({ getValue }) => n(getValue(), 'percentFine'),
        meta: {
            headerClass: 'w-1/12 text-center',
            cellClass: 'w-1/12 text-center',
        },
    }),
    columnHelper.accessor(row => row.company.name, {
        id: 'companies.name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: t('company.name'),
            }),
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
        :loading="loading"
        :on-row-click="handleRowClick"
        :on-sorting-change="handleSortingChange"
        :sorting="sorting"
        body-id="index-holding-table-body"
        infinite-scroll-key="companies"
        remember-scroll-key="IndexHoldingTable"
    />
</template>
