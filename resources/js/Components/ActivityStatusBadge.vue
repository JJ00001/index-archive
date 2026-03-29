<script lang="ts"
        setup>
import {computed} from 'vue'
import {Minus, Plus} from 'lucide-vue-next'
import {useI18n} from 'vue-i18n'
import {Badge} from '@/Components/ui/badge'
import type {IndexActivityDescription} from '@/interfaces/IndexActivity'

const props = defineProps<{
    description: IndexActivityDescription
    count?: number
}>()

const {t} = useI18n()

const activityBadgeStyles = {
    company_added_to_index: {
        badgeClass: 'gap-1.5 rounded-md border border-transparent bg-emerald-50 px-2 py-1 font-medium text-emerald-700 shadow-none',
        iconClass: 'size-3.5 text-emerald-700',
        icon: Plus,
    },
    company_removed_from_index: {
        badgeClass: 'gap-1.5 rounded-md border border-transparent bg-rose-50 px-2 py-1 font-medium text-rose-700 shadow-none',
        iconClass: 'size-3.5 text-rose-700',
        icon: Minus,
    },
} satisfies Record<IndexActivityDescription, {
    badgeClass: string
    iconClass: string
    icon: typeof Plus
}>

const badgeStyle = computed(() => activityBadgeStyles[props.description])

const badgeText = computed(() => {
    if (props.count !== undefined) {
        const sign = props.description === 'company_added_to_index' ? '+' : '-'

        return `${sign}${props.count}`
    }

    return t(`activity.${props.description}`)
})
</script>

<template>
    <Badge
        :class="badgeStyle.badgeClass"
        variant="outline"
    >
        <component
            :is="badgeStyle.icon"
            v-if="count === undefined"
            :class="badgeStyle.iconClass"
        />
        {{ badgeText }}
    </Badge>
</template>
