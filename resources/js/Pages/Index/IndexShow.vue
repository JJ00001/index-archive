<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import Breadcrumbs from '@/Components/Breadcrumbs.vue'
import { useI18n } from 'vue-i18n'

const props = defineProps({
    index: {
        type: Object,
        required: true,
    },
    stats: {
        type: Array,
        required: true,
    },
})

const { t } = useI18n()

const breadcrumbItems = [
    {
        label: t('index.name'),
        route: '/indices',
    },
    {
        label: props.index.name,
        route: null,
    },
]
</script>

<template>
    <layout-main>
        <breadcrumbs :items="breadcrumbItems" />

        <div class="space-y-10">
            <h1 class="text-3xl font-bold">{{ index.name }}</h1>
            <StatCardGroup :stats="stats" />
        </div>
    </layout-main>
</template>