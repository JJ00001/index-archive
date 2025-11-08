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

const resolveNextSorting = (updater) => {
    const previousSorting = sorting.value
    const updatedSorting = typeof updater === 'function' ? updater(previousSorting) : updater

    return updatedSorting ?? []
}

const pushSortingToServer = (columnSort) => {
    if (!columnSort?.id) {
        return
    }

    const columnName = columnSort.id

    const sortParam = columnSort.desc ? `-${columnName}` : columnName

    router.get(
        route('companies.index'),
        { sort: sortParam },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    )
}

const handleSortingChange = (updater) => {
    const nextSorting = resolveNextSorting(updater)

    sorting.value = nextSorting

    pushSortingToServer(nextSorting[0])
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
