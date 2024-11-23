<script setup>
import { WhenVisible } from "@inertiajs/vue3";
import LayoutMain from "@/Layouts/LayoutMain.vue";
import { ref } from "vue";
import CompanyIndexTable from "@/Components/CompanyIndexTable.vue";
import { Card } from "primevue";

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

const handleSuccess = (response) => {
    companyData.value = companyData.value.concat(response.props.companies.data);
};
</script>

<template>
    <layout-main>
        <card>
            <template #content>
                <company-index-table :company-data="companyData"/>
                <WhenVisible always :buffer="1000" :params="{
                    data: {
                        page: nextPage,
                    },
                    preserveUrl: true,
                    onSuccess: handleSuccess,
                }">
                    <template #default>
                        Lädt weitere Unternehmen...
                    </template>
                </WhenVisible>
            </template>
        </card>
    </layout-main>
</template>
