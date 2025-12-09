<script lang="ts"
        setup>
import {Head} from '@inertiajs/vue3'
import {useI18n} from 'vue-i18n'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import IndexIndexTable from '@/Components/tables/IndexIndexTable.vue'
import {Card, CardContent} from '@/Components/ui/card'
import type {Index} from "@/interfaces";

defineProps<{
    indices: Index[]
}>()

const { t } = useI18n()
</script>

<template>
    <layout-main>
        <Head :title="t('index.name', 2)" />
        <Card>
            <CardContent>
                <index-index-table :indices="indices" />
            </CardContent>
        </Card>
    </layout-main>
</template>