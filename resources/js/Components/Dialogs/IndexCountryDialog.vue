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
const country = ref(null)
const countryData = computed(() => country.value)

const fetchCountry = async (countryId) => {
    const response = await fetch(route('api.indices.countries.show', { index: props.indexId, country: countryId }))

    if (!response.ok) {
        throw new Error(`Request failed with status ${response.status}`)
    }

    const data = await response.json()

    country.value = data.props.country
}

const open = async (countryId) => {
    country.value = null
    dialogState.open()

    try {
        await fetchCountry(Number(countryId))
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
    if (!countryData.value) {
        return []
    }

    return [
        {
            title: t('company.count'),
            value: countryData.value.companies_count ?? 0,
        },
        {
            title: t('weight'),
            value: n(Number(countryData.value.weight ?? 0), 'percent'),
        },
    ]
})

const weightHistory = computed(() => countryData.value?.weight_history ?? [])
</script>

<template>
    <IndexEntityDialogBase :state="dialogState"
                           :title="t('country.name')">
        <template #default>
            <div v-if="countryData"
                 class="space-y-6">
                <div>
                    <h2 class="text-2xl font-bold">{{ countryData.name }}</h2>
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
