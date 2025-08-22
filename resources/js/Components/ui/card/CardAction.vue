<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: null, required: false },
})
</script>

<template>
    <div
        :class="
      cn(
        'col-start-2 row-span-2 row-start-1 self-start justify-self-end',
        props.class,
      )
    "
        data-slot="card-action"
    >
        <slot />
    </div>
</template>
