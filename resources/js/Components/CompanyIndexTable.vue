<script setup>
import { router } from '@inertiajs/vue3'
import CompanyDisplay from '@/Components/CompanyDisplay.vue'
import { ref } from 'vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'

const props = defineProps({
    companies: {
        type: Object,
        required: true,
    },
})

const companyData = ref([...props.companies.data])

const handleRowClick = (company) => {
    router.get(route('companies.show', company.id))
}
</script>

<template>
    <div class="h-[550px] overflow-auto">
        <Table>
            <TableHeader>
                <TableRow>
                    <TableHead class="w-1/12">{{ $t('rank') }}</TableHead>
                    <TableHead class="w-10/12">{{ $t('company.name') }}</TableHead>
                    <TableHead class="w-1/12">{{ $t('weight') }}</TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow
                    v-for="company in companyData"
                    :key="company.id"
                    class="cursor-pointer hover:bg-muted/50"
                    @click="handleRowClick(company)"
                >
                    <TableCell class="font-mono">
                        {{ company.rank }}
                    </TableCell>
                    <TableCell>
                      <CompanyDisplay :company="company" />
                    </TableCell>
                    <TableCell>
                        {{ $n(company.weight, 'percentFine') }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
