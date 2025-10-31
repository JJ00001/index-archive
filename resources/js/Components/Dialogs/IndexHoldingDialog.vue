<script setup>
import { computed, ref } from 'vue'
import IndexEntityDialogBase from '@/Components/Dialogs/IndexEntityDialogBase.vue'
import useIndexEntityDialog from '@/composables/useIndexEntityDialog'
import { useI18n } from 'vue-i18n'
import CompanyInfoHeader from '@/Components/CompanyInfoHeader.vue'
import WeightChart from '@/Components/Charts/WeightChart.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'

const dialogState = useIndexEntityDialog()
const indexHolding = ref(null)
const holdingData = computed(() => indexHolding.value)

const fetchIndexHolding = async (indexHoldingId) => {
    const response = await fetch(route('api.index-holdings.show', { indexHolding: indexHoldingId }))

    if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`)
    }

    const data = await response.json()

    indexHolding.value = data.props.indexHolding
}

const open = async (indexHoldingId) => {
    indexHolding.value = null
    dialogState.open()

    try {
        await fetchIndexHolding(Number(indexHoldingId))
    } catch (error) {
        dialogState.error.value = error
    } finally {
        dialogState.isLoading.value = false
    }
}

defineExpose({
    open,
})

const { t } = useI18n()
const chartData = computed(() => holdingData.value?.weight_history ?? [])
</script>

<template>
    <IndexEntityDialogBase :state="dialogState"
                           title="Index Holding Details">
        <template #default>
            <div v-if="holdingData"
                 class="space-y-6">
                <CompanyInfoHeader :company="holdingData.company" />
                <Card>
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold">{{ t('weight') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <WeightChart :data="chartData"
                                     class="h-60" />
                    </CardContent>
                </Card>
            </div>
        </template>
    </IndexEntityDialogBase>
</template>
