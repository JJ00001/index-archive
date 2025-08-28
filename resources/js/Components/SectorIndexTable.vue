<script setup>
import { router } from '@inertiajs/vue3'
import { Progress } from '@/Components/ui/progress'
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
                <TableHead class="w-1/3">{{ $t('sector.name') }}</TableHead>
                <TableHead class="w-1/3">{{ $t('weight') }}</TableHead>
                <TableHead class="w-1/3">{{ $t('company.count') }}</TableHead>
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
