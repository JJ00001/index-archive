<script setup>
import LayoutWrapper from "@/Layouts/LayoutWrapper.vue";
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import { router } from "@inertiajs/vue3";

defineProps({
    companies: {
        type: Array,
        required: true
    }
});

const handleRowClick = (event) => {
    const companyId = event.data.id;
    router.get(route('companies.show', companyId));
};
</script>

<template>
    <layout-wrapper>
        <data-table :value="companies" @row-click="handleRowClick" selection-mode="single">
            <column header="Logo">
                <template #body="{ data }">
                    <img :src="data.logo" alt="" style="max-width: 6rem; max-height: 3rem;"/>
                </template>
            </column>
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
