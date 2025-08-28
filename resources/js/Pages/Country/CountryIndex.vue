<script setup>
import LayoutMain from '@/Layouts/LayoutMain.vue'
import { Card, CardContent } from '@/Components/ui/card'
import CountryIndexTable from '@/Components/CountryIndexTable.vue'
import { ref } from 'vue'

const props = defineProps({
    countries: {
        type: Object,
        required: true
    },
})

const countryData = ref([...props.countries]);
</script>

<template>
    <layout-main>
        <Card>
            <CardContent>
                <country-index-table :country-data="countryData" />
            </CardContent>
        </Card>
    </layout-main>
</template>
