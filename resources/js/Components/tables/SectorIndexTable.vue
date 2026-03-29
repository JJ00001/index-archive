<script lang="ts"
        setup>
import {router} from '@inertiajs/vue3'
import AllocationWeightBar from '@/Components/AllocationWeightBar.vue'
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from '@/Components/ui/table'
import type {Sector} from '@/interfaces/Sector'

const props = defineProps<{
    sectorData: Sector[]
    onRowClick?: ((sector: Sector) => void) | null
}>()

const handleRowClick = (sector: Sector): void => {
    if (props.onRowClick) {
        props.onRowClick(sector)
    } else {
        router.get(route('sectors.show', sector.id))
    }
}
</script>

<template>
    <div class="max-h-[50vh] overflow-auto">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-2/12">{{ $t('sector.name') }}</TableHead>
                    <TableHead class="w-8/12">{{ $t('weight') }}</TableHead>
                    <TableHead class="w-2/12 text-right">{{ $t('company.count') }}</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="sector in sectorData"
                    :key="sector.id"
                    class="cursor-pointer"
                    @click="handleRowClick(sector)"
                >
                    <TableCell>
                        {{ sector.name }}
                    </TableCell>
                    <TableCell>
                        <div class="flex items-center gap-3">
                            <AllocationWeightBar :items="sectorData"
                                                 :weight="sector.weight" />
                            <span class="font-financial text-right text-sm text-foreground">
                                {{ $n(Number(sector.weight), 'percent') }}
                            </span>
                        </div>
                    </TableCell>
                    <TableCell class="font-financial text-right text-sm text-muted-foreground">
                        {{ sector.companies_count }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
