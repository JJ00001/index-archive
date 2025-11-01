<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import IndexIndexTable from '@/Components/tables/IndexIndexTable.vue'
import { Card, CardContent } from '@/Components/ui/card'

const props = defineProps({
    indices: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <layout-main>
        <Card>
            <CardContent>
                <index-index-table :indices="indices" />
            </CardContent>
        </Card>
    </layout-main>
</template>