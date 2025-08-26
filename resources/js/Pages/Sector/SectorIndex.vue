<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import { Card, CardContent } from '@/Components/ui/card'
import SectorIndexTable from '@/Components/SectorIndexTable.vue'
import { ref } from 'vue'

const props = defineProps({
    sectors: {
        type: Object,
        required: true
    },
})

const sectorData = ref([...props.sectors]);
</script>

<template>
    <layout-main>
        <Card>
            <CardContent>
                <sector-index-table :sector-data="sectorData" />
            </CardContent>
        </Card>
    </layout-main>
</template>
