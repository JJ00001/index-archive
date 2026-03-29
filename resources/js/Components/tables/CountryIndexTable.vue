<script lang="ts"
        setup>
import {router} from '@inertiajs/vue3'
import AllocationWeightBar from '@/Components/AllocationWeightBar.vue'
import {Table, TableBody, TableCell, TableHead, TableHeader, TableRow} from '@/Components/ui/table'
import type {Country} from '@/interfaces/Country'

const props = defineProps<{
    countryData: Country[]
    onRowClick?: ((country: Country) => void) | null
}>()

const handleRowClick = (country: Country): void => {
    if (props.onRowClick) {
        props.onRowClick(country)
    } else {
        router.get(route('countries.show', country.id))
    }
}
</script>

<template>
    <div class="max-h-[50vh] overflow-auto">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-2/12">{{ $t('country.name') }}</TableHead>
                    <TableHead class="w-8/12">{{ $t('weight') }}</TableHead>
                    <TableHead class="w-2/12 text-right">{{ $t('company.count') }}</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="country in countryData"
                    :key="country.id"
                    class="cursor-pointer"
                    @click="handleRowClick(country)"
                >
                    <TableCell>
                        {{ country.name }}
                    </TableCell>
                    <TableCell>
                        <div class="flex items-center gap-3">
                            <AllocationWeightBar :items="countryData"
                                                 :weight="country.weight" />
                            <span class="font-financial text-right text-sm text-foreground">
                                {{ $n(Number(country.weight), 'percent') }}
                            </span>
                        </div>
                    </TableCell>
                    <TableCell class="font-financial text-right text-sm text-muted-foreground">
                        {{ country.companies_count }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
