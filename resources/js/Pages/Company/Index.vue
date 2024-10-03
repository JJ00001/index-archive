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
            <column field="market_datas" header="Gewichtung in %">
                <template #body="slotProps">
                    {{ slotProps.data.market_datas[0].weight }} %
                </template>
            </column>
            <column field="sector.name" header="Branche"/>
            <column field="country.name" header="Land"/>
            <column field="exchange.name" header="Börsenplatz"/>
        </data-table>
    </layout-wrapper>
</template>
