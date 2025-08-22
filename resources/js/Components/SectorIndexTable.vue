<script setup>
import { router } from '@inertiajs/vue3'
import { Progress } from '@/components/ui/progress'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'

defineProps({
    sectorData: {
        type: Array,
        required: true,
    },
})

const handleRowClick = (sector) => {
    router.get(route('sectors.show', sector.id))
}
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead class="w-1/3">Name</TableHead>
                <TableHead class="w-1/3">Gewichtung</TableHead>
                <TableHead class="w-1/3">Anzahl Unternehmen</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow
                v-for="sector in sectorData"
                :key="sector.id"
                class="cursor-pointer hover:bg-muted/50"
                @click="handleRowClick(sector)"
            >
                <TableCell>{{ sector.name }}</TableCell>
                <TableCell>
                    {{ $n(Number(sector.weight), 'percent') }}
                    <Progress :model-value="sector.weight * 100"
                              class="h-2" />
                </TableCell>
                <TableCell>{{ sector.companies_count }}</TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
