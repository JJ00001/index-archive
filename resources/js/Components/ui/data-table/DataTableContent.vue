<script setup>
import { FlexRender } from '@tanstack/vue-table'
import { ChevronDown, ChevronRight } from 'lucide-vue-next'
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import DataTableRowSkeleton from '@/Components/ui/data-table/DataTableRowSkeleton.vue'
import { Button } from '@/Components/ui/button'

const props = defineProps({
    table: {
        type: Object,
        required: true,
    },
    loading: {
        type: Boolean,
        required: true,
    },
    loadingInfiniteScroll: {
        type: Boolean,
        default: false,
    },
    rowClasses: {
        type: String,
        default: '',
    },
    handleRowClick: {
        type: Function,
        required: true,
    },
    formatGroupHeader: {
        type: Function,
        default: null,
    },
    emptyStateMessage: {
        type: String,
        required: true,
    },
    skeletonRowCount: {
        type: Number,
        required: true,
    },
    bodyId: {
        type: String,
        required: true,
    },
})

const getGroupedCellRender = (cell) => cell.column.columnDef.aggregatedCell ?? cell.column.columnDef.cell

const getGroupHeader = (row) => {
    if (!props.formatGroupHeader) {
        return {
            label: row.groupingValue,
            details: [
                {
                    parts: [
                        {
                            text: `(${row.subRows.length})`,
                            class: 'text-muted-foreground text-xs',
                        },
                    ],
                },
            ],
        }
    }

    return props.formatGroupHeader(row)
}
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow
                v-for="headerGroup in table.getHeaderGroups()"
                :key="headerGroup.id"
            >
                <TableHead
                    v-for="header in headerGroup.headers"
                    :key="header.id"
                    :class="header.column.columnDef.meta?.headerClass"
                >
                    <template v-if="!header.isPlaceholder">
                        <FlexRender
                            :props="header.getContext()"
                            :render="header.column.columnDef.header"
                        />
                    </template>
                </TableHead>
            </TableRow>
        </TableHeader>
        <TableBody :id="bodyId">
            <template v-if="table.getRowModel().rows.length && !loading">
                <TableRow
                    v-for="row in table.getRowModel().rows"
                    :key="row.id"
                    :class="row.getIsGrouped() ? '' : rowClasses"
                    @click="!row.getIsGrouped() && handleRowClick(row)"
                >
                    <TableCell
                        v-if="row.getIsGrouped()"
                        :colspan="table.getVisibleLeafColumns().length"
                    >
                        <Button
                            class="h-auto justify-start gap-2 px-0 py-0 font-medium hover:bg-transparent"
                            type="button"
                            variant="ghost"
                            @click.stop="row.getToggleExpandedHandler()()"
                        >
                            <component
                                :is="row.getIsExpanded() ? ChevronDown : ChevronRight"
                                class="h-4 w-4 shrink-0"
                            />
                            <span>{{ getGroupHeader(row).label }}</span>
                            <span
                                v-for="detail in getGroupHeader(row).details ?? []"
                                :key="detail.key ?? JSON.stringify(detail.parts ?? detail.props)"
                                :class="detail.class"
                            >
                                <component
                                    :is="detail.component"
                                    v-if="detail.component"
                                    v-bind="detail.props"
                                />
                                <template
                                    v-for="part in detail.parts ?? []"
                                    v-else
                                    :key="part.key ?? part.text"
                                >
                                    <span :class="part.class">{{ part.text }}</span>
                                </template>
                            </span>
                        </Button>
                    </TableCell>
                    <TableCell
                        v-else
                        v-for="cell in row.getVisibleCells()"
                        :key="cell.id"
                        :class="cell.column.columnDef.meta?.cellClass"
                    >
                        <FlexRender
                            v-if="cell.getIsAggregated()"
                            :props="cell.getContext()"
                            :render="getGroupedCellRender(cell)"
                        />
                        <span
                            v-else-if="cell.getIsPlaceholder()"
                            aria-hidden="true"
                            class="block h-4"
                        />
                        <FlexRender
                            v-else
                            :props="cell.getContext()"
                            :render="cell.column.columnDef.cell"
                        />
                    </TableCell>
                </TableRow>
            </template>
            <TableEmpty v-else-if="!(loading || loadingInfiniteScroll)"
                        :colspan="table.getVisibleLeafColumns().length">
                {{ emptyStateMessage }}
            </TableEmpty>
            <DataTableRowSkeleton
                v-if="loading || loadingInfiniteScroll"
                :columns="table.getAllLeafColumns()"
                :row-count="skeletonRowCount"
            />
        </TableBody>
    </Table>
</template>
