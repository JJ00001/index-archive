<script setup>
import { FlexRender } from '@tanstack/vue-table'
import { Table, TableBody, TableCell, TableEmpty, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import DataTableRowSkeleton from '@/Components/ui/data-table/DataTableRowSkeleton.vue'

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
                    :class="rowClasses"
                    @click="handleRowClick(row)"
                >
                    <TableCell
                        v-for="cell in row.getVisibleCells()"
                        :key="cell.id"
                        :class="cell.column.columnDef.meta?.cellClass"
                    >
                        <FlexRender
                            :props="cell.getContext()"
                            :render="cell.column.columnDef.cell"
                        />
                    </TableCell>
                </TableRow>
            </template>
            <TableEmpty v-else-if="!(loading || loadingInfiniteScroll)"
                        :colspan="table.getAllColumns().length">
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
