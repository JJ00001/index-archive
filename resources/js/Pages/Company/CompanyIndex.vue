<script setup>
import { Head } from '@inertiajs/vue3'
import { useI18n } from 'vue-i18n'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyIndexTable from '@/Components/tables/CompanyIndexTable.vue'
import { Card, CardContent } from '@/Components/ui/card'

const props = defineProps({
    companies: {
        type: Object,
        required: true,
    },
})

const { t } = useI18n()
</script>

<template>
    <layout-main>
        <Head :title="t('company.name', 2)" />
        <Card>
            <CardContent>
                <company-index-table :companies="companies" />
            </CardContent>
        </Card>
    </layout-main>
</template>
