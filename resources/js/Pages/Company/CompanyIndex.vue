<script lang="ts"
        setup>
import {Head} from '@inertiajs/vue3'
import {useI18n} from 'vue-i18n'
import LayoutMain from '@/Layouts/LayoutMain.vue'
import CompanyIndexTable from '@/Components/tables/CompanyIndexTable.vue'
import {Card, CardContent} from '@/Components/ui/card'
import type {Company} from "@/interfaces/Company.ts";
import type {Paginated} from "@/interfaces/Paginated.ts";

defineProps<{
    companies: Paginated<Company>,
    sort: Object,
}>()

const { t } = useI18n()
</script>

<template>
    <layout-main>
        <Head :title="t('company.name', 2)" />
        <Card>
            <CardContent>
                <company-index-table
                    :companies="companies"
                    :sort="sort"
                />
            </CardContent>
        </Card>
    </layout-main>
</template>
