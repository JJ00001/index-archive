<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import { Card } from "primevue";
import SectorIndexTable from "@/Components/SectorIndexTable.vue";
import { ref } from "vue";

const props = defineProps({
    sectors: {
        type: Object,
        required: true
    }
})

const sectorData = ref([...props.sectors]);
</script>

<template>
    <layout-main>
        <card>
            <template #content>
                <sector-index-table :sector-data="sectorData"/>
            </template>
        </card>
    </layout-main>
</template>
