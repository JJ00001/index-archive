import {computed, type ComputedRef, type MaybeRefOrGetter, toValue} from 'vue'

export type WeightedAllocationItem = {
    weight?: number | string | null
}

export function useAllocationWeight<T extends WeightedAllocationItem>(
    items: MaybeRefOrGetter<T[]>,
): {
    maxWeight: ComputedRef<number>
    normalizedWeight: (weight: T['weight']) => string
} {
    const maxWeight = computed(() => {
        return toValue(items).reduce((largestWeight, item) => {
            const currentWeight = Number(item.weight ?? 0)

            return currentWeight > largestWeight ? currentWeight : largestWeight
        }, 0)
    })

    const normalizedWeight = (weight: T['weight']): string => {
        const numericWeight = Number(weight ?? 0)

        if (!maxWeight.value || numericWeight <= 0) {
            return '0%'
        }

        return `${(numericWeight / maxWeight.value) * 100}%`
    }

    return {
        maxWeight,
        normalizedWeight,
    }
}
