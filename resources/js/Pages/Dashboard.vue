<script setup>
import DashboardHero from '@/Components/DashboardHero.vue'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import IndexIndexTable from '@/Components/IndexIndexTable.vue'

const props = defineProps({
    indices: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <layout-main>
        <DashboardHero />
        <index-index-table :indices="indices" />
    </layout-main>
</template>
