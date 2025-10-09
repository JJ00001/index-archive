<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyIndexTable from '@/Components/CompanyIndexTable.vue'
import { Card, CardContent } from '@/Components/ui/card'

const props = defineProps({
    companies: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <layout-main>
        <Card>
            <CardContent>
                <company-index-table :companies="companies" />
            </CardContent>
        </Card>
    </layout-main>
</template>
