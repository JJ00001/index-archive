<script setup>
import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'
import IndexEntityDialogBase from '@/Components/Dialogs/IndexEntityDialogBase.vue'
import useIndexEntityDialog from '@/composables/useIndexEntityDialog'
import StatCardGroup from '@/Components/StatCardGroup.vue'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'

const props = defineProps({
    indexId: {
        type: [Number, String],
        required: true,
    },
})

const dialogState = useIndexEntityDialog()
const sector = ref(null)
const sectorData = computed(() => sector.value)

const fetchSector = async (sectorId) => {
    const response = await fetch(route('api.indices.sectors.show', { index: props.indexId, sector: sectorId }))

    if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`)
    }

    const data = await response.json()

    sector.value = data.props.sector
}

const open = async (sectorId) => {
    sector.value = null
    dialogState.open()

    try {
        await fetchSector(Number(sectorId))
    } catch (error) {
        dialogState.error.value = error
    } finally {
        dialogState.isLoading.value = false
    }
}

defineExpose({
    open,
})

const { n, t } = useI18n()

const stats = computed(() => {
    if (!sectorData.value) {
        return []
    }

    return [
        {
            title: t('company.count'),
            value: sectorData.value.companies_count ?? 0,
        },
        {
            title: t('weight'),
            value: n(Number(sectorData.value.weight ?? 0), 'percent'),
        },
    ]
})

const weightHistory = computed(() => sectorData.value?.weight_history ?? [])
</script>

<template>
    <IndexEntityDialogBase :state="dialogState"
                           :title="t('sector.name')">
        <template #default>
            <div v-if="sectorData"
                 class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold">{{ sectorData.name }}</h2>
                </div>
                <StatCardGroup :stats="stats" />
                <Card>
                    <CardHeader>
                        <CardTitle class="text-xl font-semibold">{{ t('weight') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <WeightChart :data="weightHistory"
                                     class="h-60" />
                    </CardContent>
                </Card>
            </div>
        </template>
    </IndexEntityDialogBase>
</template>
