<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
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
</script>

<template>
    <layout-main>
        <card>
            <template #content>
                <company-index-table :companies="companies" :next-page="nextPage"/>
            </template>
        </card>
    </layout-main>
</template>
