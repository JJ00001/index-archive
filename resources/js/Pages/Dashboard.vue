<script setup>
import { Head } from '@inertiajs/vue3'
import DashboardHero from '@/Components/DashboardHero.vue'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import IndexIndexTable from '@/Components/tables/IndexIndexTable.vue'

const props = defineProps({
    indices: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <layout-main>
        <Head title="Dashboard" />
        <DashboardHero />
        <index-index-table :indices="indices" />
    </layout-main>
</template>
