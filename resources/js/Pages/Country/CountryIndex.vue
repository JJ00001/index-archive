<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import { Card } from "primevue";
import CountryIndexTable from "@/Components/CountryIndexTable.vue";
import { ref } from "vue";

const props = defineProps({
    countries: {
        type: Object,
        required: true
    }
})

const countryData = ref([...props.countries]);
</script>

<template>
    <layout-main>
        <card>
            <template #content>
                <country-index-table :country-data="countryData"/>
            </template>
        </card>
    </layout-main>
</template>
