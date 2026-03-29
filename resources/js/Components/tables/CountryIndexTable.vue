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
          <TableHead class="w-4/6">{{ $t('country.name') }}</TableHead>
          <TableHead class="w-1/6">{{ $t('weight') }}</TableHead>
          <TableHead class="w-1/6">{{ $t('company.count') }}</TableHead>
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
  </div>
</template>
