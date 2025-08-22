<script setup>
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import { router } from '@inertiajs/vue3'
import { ProgressBar } from 'primevue'

defineProps({
    countryData: {
        type: Array,
        required: true
    }
});

const handleRowClick = (event) => {
    const countryId = event.data.id;
    router.get(route('countries.show', countryId));
};
</script>

<template>
    <data-table :value="countryData" selection-mode="single" @row-click="handleRowClick">
        <column class="w-1/3" field="name" header="Name"/>
        <column class="w-1/3" field="weight" header="Gewichtung">
            <template #body="{ data }">
                {{ $n(Number(data.weight), 'percent') }}
              <ProgressBar :show-value="false"
                           :value="data.weight * 100"
                           class="h-2!" />
            </template>
        </column>
        <column class="w-1/3" field="companies_count" header="Anzahl Unternehmen"/>
    </data-table>
</template>
