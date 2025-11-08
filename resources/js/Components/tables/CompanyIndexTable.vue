<script setup>
import { router } from '@inertiajs/vue3'
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
    sort: {
        type: Object,
        required: true,
    },
    onRowClick: {
        type: Function,
        default: null,
    },
})

const { t } = useI18n()
const columnHelper = createColumnHelper()

const visitCompaniesIndex = (params = {}) => {
    router.get(
        route('companies.index'),
        params,
        {
            reset: ['companies'],
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    )
}

const sort = computed(() => props.sort)
const { sorting, handleSortingChange } = useDataTableSorting(sort, visitCompaniesIndex)

const columns = [
    columnHelper.accessor('name', {
        id: 'name',
        header: ({ column }) =>
            h(DataTableColumnHeader, {
                column,
                title: t('company.name'),
            }),
        cell: ({ row }) => h(CompanyDisplay, { company: row.original }),
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
        :on-sorting-change="handleSortingChange"
        :sorting="sorting"
        :infinite-scroll-buffer="500"
        :on-row-click="handleRowClick"
        body-id="company-index-table-body"
        infinite-scroll-key="companies"
        remember-scroll-key="CompanyIndexTable"
    />
</template>
