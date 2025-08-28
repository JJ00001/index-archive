<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: null, required: false },
})
</script>

<template>
    <thead :class="cn('[&_tr]:border-b', props.class)"
           data-slot="table-header">
    <slot />
    </thead>
</template>
