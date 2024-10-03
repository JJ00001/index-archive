<script setup>
import LayoutWrapper from "@/Layouts/LayoutWrapper.vue";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';

defineProps({
    companies: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <layout-wrapper>
        <data-table :value="companies" striped-rows>
            <column field="ticker" header="Ticker"/>
            <column field="name" header="Name"/>
            <column field="latest_weight" header="Gewichtung">
                <template #body="{ data }">
                    {{ data.latest_weight }} %
                </template>
            </column>
            <column field="sector.name" header="Branche"/>
            <column field="country.name" header="Land"/>
            <column field="exchange.name" header="Börsenplatz"/>
        </data-table>
    </layout-wrapper>
</template>
