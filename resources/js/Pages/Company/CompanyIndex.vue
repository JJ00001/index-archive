<script setup>
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import { router, WhenVisible } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import { ref } from "vue";

const props = defineProps({
    companies: {
        type: Object,
        required: true
    },
    nextPage: {
        type: Number,
        required: true
    }
});

const companyData = ref([...props.companies.data]);

const handleRowClick = (event) => {
    const companyId = event.data.id;
    router.get(route('companies.show', companyId));
};

const handleSuccess = (response) => {
    companyData.value = companyData.value.concat(response.props.companies.data);
};
</script>

<template>
    <layout-main>
        <data-table :value="companyData" @row-click="handleRowClick" selection-mode="single">
            <column header="Unternehmen" class="w-5/12">
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
            <column field="latest_weight" header="Gewichtung" class="w-1/12">
                <template #body="{ data }">
                    {{ data.latest_weight }} %
                </template>
            </column>
            <column field="sector.name" header="Branche" class="w-2/12"/>
            <column field="country.name" header="Land" class="w-2/12"/>
            <column field="exchange.name" header="Börsenplatz" class="w-2/12"/>
        </data-table>
        <WhenVisible always :params="{
            data: {
                page: nextPage,
            },
            preserveUrl: true,
            onSuccess: handleSuccess,
        }" :buffer="1000">
            <template #default>
                Lädt weitere Unternehmen...
            </template>
        </WhenVisible>
    </layout-main>
</template>
