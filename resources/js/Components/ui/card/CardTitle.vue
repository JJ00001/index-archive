<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: null, required: false },
})
</script>

<template>
    <h3
        :class="cn('leading-none font-semibold', props.class)"
        data-slot="card-title"
    >
        <slot />
    </h3>
</template>
