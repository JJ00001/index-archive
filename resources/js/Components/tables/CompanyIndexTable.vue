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

const { t } = useI18n()
const columnHelper = createColumnHelper()

const columns = [
    columnHelper.display({
        id: 'company',
        header: () => t('company.name'),
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
        :infinite-scroll-buffer="500"
        :on-row-click="handleRowClick"
        body-id="company-index-table-body"
        infinite-scroll-key="companies"
        remember-scroll-key="CompanyIndexTable"
    />
</template>
