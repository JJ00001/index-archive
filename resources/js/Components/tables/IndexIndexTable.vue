<script lang="ts"
        setup>
import {ref} from 'vue'
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from '@/Components/ui/table'
import {router} from '@inertiajs/vue3'
import type {Index} from "@/interfaces";

const props = defineProps<{
    indices: Index[],
}>()

const indexData = ref<Index[]>([...props.indices])

const handleRowClick = (index: Index): void => {
    router.get(route('indices.show', index.id))
}
</script>

<template>
    <div class="max-h-[550px] overflow-auto">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-6/12">{{ $t('index.name') }}</TableHead>
                    <TableHead class="w-3/12">{{ $t('indexProvider.name') }}</TableHead>
                    <TableHead class="w-1/12">{{ $t('indexHolding.name', 2) }}</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="index in indexData"
                    :key="index.id"
                    class="cursor-pointer hover:bg-muted/50"
                    @click="handleRowClick(index)"
                >
                    <TableCell>
                        {{ index.name }}
                    </TableCell>
                    <TableCell>
                        {{ index.index_provider?.name }}
                    </TableCell>
                    <TableCell>
                        {{ index.index_holdings_count || '-' }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>