<script setup>
import { router } from '@inertiajs/vue3'
import { Progress } from '@/Components/ui/progress'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'

defineProps({
    countryData: {
        type: Array,
        required: true,
    },
})

const handleRowClick = (country) => {
    router.get(route('countries.show', country.id))
}
</script>

<template>
    <Table>
        <TableHeader>
            <TableRow>
                <TableHead class="w-1/3">{{ $t('country.name') }}</TableHead>
                <TableHead class="w-1/3">{{ $t('weight') }}</TableHead>
                <TableHead class="w-1/3">{{ $t('company.count') }}</TableHead>
            </TableRow>
        </TableHeader>
        <TableBody>
            <TableRow
                v-for="country in countryData"
                :key="country.id"
                class="cursor-pointer hover:bg-muted/50"
                @click="handleRowClick(country)"
            >
                <TableCell>{{ country.name }}</TableCell>
                <TableCell>
                    {{ $n(Number(country.weight), 'percent') }}
                    <Progress :model-value="country.weight * 100"
                              class="h-2" />
                </TableCell>
                <TableCell>{{ country.companies_count }}</TableCell>
            </TableRow>
        </TableBody>
    </Table>
</template>
