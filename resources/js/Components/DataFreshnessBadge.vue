<script lang="ts"
        setup>
import {computed} from 'vue'
import {Badge} from '@/Components/ui/badge'

const props = defineProps<{
    date: string | null
}>()

const label = computed(() => {
    if (!props.date) {
        return 'Awaiting first snapshot'
    }

    const date = new Date(`${props.date}T00:00:00`)
    const formattedDate = new Intl.DateTimeFormat('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    }).format(date)

    return `Updated ${formattedDate}`
})
</script>

<template>
    <Badge class="gap-1.5 px-2.5 py-1 text-xs font-normal text-muted-foreground"
           variant="secondary">
        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" />
        {{ label }}
    </Badge>
</template>
