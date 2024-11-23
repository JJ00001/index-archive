<script setup>
import DataTable from "primevue/datatable";
import Column from "primevue/column";

defineProps({
    countryData: {
        type: Array,
        required: true
    }
});

const handleRowClick = () => {
    alert('Coming soon');
};
</script>

<template>
    <data-table :value="countryData" selection-mode="single" @row-click="handleRowClick">
        <column class="w-1/3" field="name" header="Name"/>
        <column class="w-1/3" field="weight" header="Gewichtung"/>
        <column class="w-1/3" field="companies_count" header="Anzahl Unternehmen"/>
    </data-table>
</template>
