<script lang="ts"
        setup>
import {useAllocationWeight, type WeightedAllocationItem} from '@/composables/useAllocationWeight'

const props = defineProps<{
    items: WeightedAllocationItem[]
    weight?: number | string | null
}>()

const {normalizedWeight} = useAllocationWeight(() => props.items)
</script>

<template>
    <div class="relative h-2 w-full overflow-hidden rounded-full bg-muted/50">
        <div
            :style="{ width: normalizedWeight(weight) }"
            class="h-full rounded-full bg-foreground/80 transition-[width]"
        />
    </div>
</template>
