<script setup>
import { Skeleton } from '@/Components/ui/skeleton'
import { TableCell, TableRow } from '@/Components/ui/table'

const props = defineProps({
    columns: {
        type: Array,
        required: true,
    },
    rowCount: {
        type: Number,
        default: 5,
    },
})
</script>

<template>
    <TableRow
        v-for="row in rowCount"
    >
        <TableCell
            v-for="column in columns.length"
            :class="column.columnDef?.meta?.cellClass"
        >
            <Skeleton class="h-10" />
        </TableCell>
    </TableRow>
</template>
