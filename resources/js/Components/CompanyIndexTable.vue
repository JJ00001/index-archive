<script setup>
import DataTable from "primevue/datatable";
import Column from 'primevue/column';
import { router, WhenVisible } from "@inertiajs/vue3";
import CompanyLogo from "@/Components/CompanyLogo.vue";
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
})

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
    <data-table :value="companyData" @row-click="handleRowClick" selection-mode="single">
        <column class="w-1/12" field="rank" header="Position"/>
        <column class="w-7/12" header="Unternehmen">
            <template #body="{ data }">
                <div class="flex items-center">
                    <div class="w-20 h-8 mr-4 flex-shrink-0">
                        <company-logo :logo-path="data.logo" class="w-full h-full"/>
                    </div>
                    <div>
                        <span class="font-bold">{{ data.name }}</span><br>
                        <span class="text-sm text-gray-500">{{ data.ticker }}</span>
                    </div>
                </div>
            </template>
        </column>
        <column class="w-2/12" field="latest_weight" header="Gewichtung">
            <template #body="{ data }">
                {{ $n(Number(data.latest_weight), 'percent') }}
            </template>
        </column>
        <column class="w-2/12" field="market_capitalization" header="Marktkap.">
            <template #body="{ data, field}">
                {{ $n(Number(data[field]), 'currencyUSDCompact') }}
            </template>
        </column>
    </data-table>
    <WhenVisible :buffer="1000" :params="{
                    data: {
                        page: nextPage,
                    },
                    preserveUrl: true,
                    onSuccess: handleSuccess,
                }" always>
        <template #default>
            Lädt weitere Unternehmen...
        </template>
    </WhenVisible>
</template>
