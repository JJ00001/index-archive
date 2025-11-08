<script setup>
import { router } from '@inertiajs/vue3'
import { createColumnHelper } from '@tanstack/vue-table'
import { h, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import DataTable from '@/Components/ui/data-table/DataTable.vue'
import DataTableColumnHeader from '@/Components/ui/data-table/DataTableColumnHeader.vue'

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
const sorting = ref([])

watch(
    () => props.sort,
    (value) => {
        sorting.value = [
            {
                id: value.column,
                desc: value.direction === 'desc',
            },
        ]
    },
)

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

const resolveNextSorting = (updater) => {
    const previousSorting = sorting.value
    const updatedSorting = updater(previousSorting)

    return updatedSorting ?? []
}

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

const requestSorting = (columnSort) => {
    const sortParam = columnSort.desc ? `-${columnSort.id}` : columnSort.id

    visitCompaniesIndex({ sort: sortParam })
}

const requestDefaultSorting = () => {
    visitCompaniesIndex()
}

const handleSortingChange = (updater) => {
    sorting.value = resolveNextSorting(updater)

    const columnSort = sorting.value[0]

    if (!columnSort?.id) {
        requestDefaultSorting()
        return
    }

    requestSorting(columnSort)
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
