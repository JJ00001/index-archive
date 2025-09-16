<script setup>
import { router } from '@inertiajs/vue3'
import CompanyLogo from '@/Components/CompanyLogo.vue'
import { ref } from 'vue'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'

const props = defineProps({
    companies: {
        type: Object,
        required: true,
    },
    nextPage: {
        type: Number,
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
                        <div class="flex items-center">
                            <div class="w-20 h-8 mr-4 shrink-0">
                                <CompanyLogo :logo-path="company.logo"
                                             class="w-full h-full" />
                            </div>
                            <div>
                                <span class="font-bold">{{ company.name }}</span>
                                <br>
                                <span class="text-sm text-muted-foreground">{{ company.ticker }}</span>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        {{ $n(company.weight, 'percentFine') }}
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>
</template>
