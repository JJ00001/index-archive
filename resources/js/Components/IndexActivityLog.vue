<script lang="ts"
        setup>
import {createColumnHelper} from '@tanstack/vue-table'
import {computed, h} from 'vue'
import ActivityStatusBadge from '@/Components/ActivityStatusBadge.vue'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import DataTable from '@/Components/ui/data-table/DataTable.vue'
import {useI18n} from 'vue-i18n'
import type {IndexActivity} from '@/interfaces/IndexActivity.ts'

const props = defineProps<{
    activities: IndexActivity[]
}>()

const { t } = useI18n()
const columnHelper = createColumnHelper<IndexActivity>()
const grouping = ['date']
const columnVisibility = {
    date: false,
}

const getMonthActivitySummary = (row: { groupingValue: string, subRows: Array<{ original: IndexActivity }> }) => {
    const activityCounts = row.subRows.reduce((counts, subRow) => {
        if (subRow.original.description === 'company_added_to_index') {
            counts.added += 1
        }

        if (subRow.original.description === 'company_removed_from_index') {
            counts.removed += 1
        }

        return counts
    }, {
        added: 0,
        removed: 0,
    })

    return {
        label: row.groupingValue,
        details: [
            {
                key: 'added-count',
                component: ActivityStatusBadge,
                props: {
                    count: activityCounts.added,
                    description: 'company_added_to_index',
                },
            },
            {
                key: 'removed-count',
                component: ActivityStatusBadge,
                props: {
                    count: activityCounts.removed,
                    description: 'company_removed_from_index',
                },
            },
        ],
    }
}

const columns = [
    columnHelper.accessor('date', {
        id: 'date',
    }),
    columnHelper.display({
        id: 'company',
        header: () => t('company.name'),
        cell: ({row}) => {
            return h(CompanyDisplay, {
                company: row.original.company,
            })
        },
        meta: {
            headerClass: 'w-11/12',
            cellClass: 'w-11/12',
        },
    }),
    columnHelper.accessor('description', {
        id: 'description',
        header: () => t('activity.status'),
        cell: ({getValue}) => {
            return h(ActivityStatusBadge, {
                description: getValue(),
            })
        },
        meta: {
            headerClass: 'w-1/12',
            cellClass: 'w-1/12',
        },
    }),
]

const activityTableData = computed(() => ({
    data: props.activities,
}))
</script>

<template>
    <div
        v-if="activities.length === 0"
        class="text-sm text-muted-foreground"
    >
        {{ $t('activity.noRecentActivity') }}
    </div>
    <DataTable
        v-else
        :column-visibility="columnVisibility"
        :columns="columns"
        :data="activityTableData"
        :expanded="true"
        :format-group-header="getMonthActivitySummary"
        :grouping="grouping"
        :loading="false"
        body-id="index-activity-log-body"
        empty-state-message=""
        remember-scroll-key="IndexActivityLog"
    />
</template>
