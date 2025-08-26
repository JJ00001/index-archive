<script setup>
import { Axis, CurveType, Line } from '@unovis/ts'
import { VisAxis, VisLine, VisXYContainer } from '@unovis/vue'
import { useMounted } from '@vueuse/core'
import { computed, ref } from 'vue'
import { cn } from '@/lib/utils'
import { ChartCrosshair, ChartLegend, defaultColors } from '@/components/ui/chart'

const props = defineProps({
    data: { type: Array, required: true },
    categories: { type: Array, required: true },
    index: { type: String, required: true },
    colors: { type: Array, required: false },
    margin: {
        type: Object,
        required: false,
        default: () => ({ top: 0, bottom: 0, left: 0, right: 0 }),
    },
    filterOpacity: { type: Number, required: false, default: 0.2 },
    xFormatter: { type: Function, required: false },
    yFormatter: { type: Function, required: false },
    showXAxis: { type: Boolean, required: false, default: true },
    showYAxis: { type: Boolean, required: false, default: true },
    showTooltip: { type: Boolean, required: false, default: true },
    showLegend: { type: Boolean, required: false, default: true },
    showGridLine: { type: Boolean, required: false, default: true },
    customTooltip: { type: null, required: false },
    curveType: { type: String, required: false, default: CurveType.MonotoneX },
})

const emits = defineEmits(['legendItemClick'])

const index = computed(() => props.index)
const colors = computed(() =>
    props.colors?.length ? props.colors : defaultColors(props.categories.length),
)

const legendItems = ref(
    props.categories.map((category, i) => ({
        name: category,
        color: colors.value[i],
        inactive: false,
    })),
)

const isMounted = useMounted()

function handleLegendItemClick (d, i) {
    emits('legendItemClick', d, i)
}
</script>

<template>
    <div
        :class="cn('w-full h-[400px] flex flex-col items-end', $attrs.class ?? '')"
    >
        <ChartLegend
            v-if="showLegend"
            v-model:items="legendItems"
            @legend-item-click="handleLegendItemClick"
        />

        <VisXYContainer
            :data="data"
            :margin="{ left: 20, right: 20 }"
            :style="{ height: isMounted ? '100%' : 'auto' }"
        >
            <ChartCrosshair
                v-if="showTooltip"
                :colors="colors"
                :custom-tooltip="customTooltip"
                :index="index"
                :items="legendItems"
            />

            <template v-for="(category, i) in categories"
                      :key="category">
                <VisLine
                    :attributes="{
            [Line.selectors.line]: {
              opacity: legendItems.find((item) => item.name === category)
                ?.inactive
                ? filterOpacity
                : 1,
            },
          }"
                    :color="colors[i]"
                    :curve-type="curveType"
                    :x="(d, i) => i"
                    :y="(d) => d[category]"
                />
            </template>

            <VisAxis
                v-if="showXAxis"
                :grid-line="false"
                :tick-format="xFormatter ?? ((v) => data[v]?.[index])"
                :tick-line="false"
                tick-text-color="hsl(var(--vis-text-color))"
                type="x"
            />
            <VisAxis
                v-if="showYAxis"
                :attributes="{
          [Axis.selectors.grid]: {
            class: 'text-muted',
          },
        }"
                :domain-line="false"
                :grid-line="showGridLine"
                :tick-format="yFormatter"
                :tick-line="false"
                tick-text-color="hsl(var(--vis-text-color))"
                type="y"
            />

            <slot />
        </VisXYContainer>
    </div>
</template>
