<script setup>
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import { router } from "@inertiajs/vue3";

defineProps({
    sectorData: {
        type: Array,
        required: true
    }
});

const handleRowClick = (event) => {
    const sectorId = event.data.id;
    router.get(route('sectors.show', sectorId));
};
</script>

<template>
    <data-table :value="sectorData" selection-mode="single" @row-click="handleRowClick">
        <column class="w-1/3" field="name" header="Name"/>
        <column class="w-1/3" field="weight" header="Gewichtung">
            <template #body="{ data }">
                {{ $n(Number(data.weight), 'percent') }}
            </template>
        </column>
        <column class="w-1/3" field="companies_count" header="Anzahl Unternehmen"/>
    </data-table>
</template>
