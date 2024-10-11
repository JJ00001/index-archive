<script setup>
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import { router } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";

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
    <layout-main>
        <data-table :value="companies" @row-click="handleRowClick" selection-mode="single">
            <column header="Unternehmen">
                <template #body="{ data }">
                    <div class="flex items-center">
                        <div class="w-20 h-8 mr-4 flex-shrink-0">
                            <img v-if="data.logo" :src="data.logo" alt="logo" class="w-full h-full object-contain"/>
                        </div>
                        <div>
                            <span class="font-bold">{{ data.name }}</span><br>
                            <span class="text-sm text-gray-500">{{ data.ticker }}</span>
                        </div>
                    </div>
                </template>
            </column>
            <column field="latest_weight" header="Gewichtung">
                <template #body="{ data }">
                    {{ data.latest_weight }} %
                </template>
            </column>
            <column field="sector.name" header="Branche"/>
            <column field="country.name" header="Land"/>
            <column field="exchange.name" header="Börsenplatz"/>
        </data-table>
    </layout-main>
</template>
