<script lang="ts"
        setup>
import {createColumnHelper} from '@tanstack/vue-table'
import {Minus, Plus} from 'lucide-vue-next'
import type {Component} from 'vue'
import {computed, h} from 'vue'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import {Badge} from '@/Components/ui/badge'
import DataTable from '@/Components/ui/data-table/DataTable.vue'
import {useI18n} from 'vue-i18n'
import type {IndexActivity, IndexActivityDescription} from '@/interfaces/IndexActivity.ts'

const props = defineProps<{
    activities: IndexActivity[]
}>()

const { t } = useI18n()
const columnHelper = createColumnHelper<IndexActivity>()
const grouping = ['date']
const columnVisibility = {
    date: false,
}

const activityStyles = {
    company_added_to_index: {
        badgeClass: 'gap-1.5 rounded-md border border-transparent bg-emerald-50 px-2 py-1 font-medium text-emerald-700 shadow-none',
        tone: 'text-emerald-700',
        icon: Plus,
    },
    company_removed_from_index: {
        badgeClass: 'gap-1.5 rounded-md border border-transparent bg-rose-50 px-2 py-1 font-medium text-rose-700 shadow-none',
        tone: 'text-rose-700',
        icon: Minus,
    },
} satisfies Record<IndexActivityDescription, {
    badgeClass: string
    tone: string
    icon: Component
}>

const getActivityStyle = (description: IndexActivityDescription) => {
    return activityStyles[description]
}

const getActivityLabel = (description: IndexActivityDescription): string => {
    return t(`activity.${description}`)
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
            headerClass: 'w-10/12',
            cellClass: 'w-10/12',
        },
    }),
    columnHelper.accessor('description', {
        id: 'description',
        header: () => t('activity.status'),
        cell: ({getValue}) => {
            const description = getValue()
            const style = getActivityStyle(description)

            return h(Badge, {
                variant: 'outline',
                class: style.badgeClass,
            }, () => [
                h(style.icon, {class: ['size-3.5', style.tone]}),
                getActivityLabel(description),
            ])
        },
        meta: {
            headerClass: 'w-2/12',
            cellClass: 'w-2/12',
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
        :grouping="grouping"
        :loading="false"
        body-id="index-activity-log-body"
        empty-state-message=""
        remember-scroll-key="IndexActivityLog"
    />
</template>
