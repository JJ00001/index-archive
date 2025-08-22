<script setup>
import { ChevronRight } from 'lucide-vue-next'
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: null, required: false },
})
</script>

<template>
    <li
        :class="cn('[&>svg]:size-3.5', props.class)"
        aria-hidden="true"
        data-slot="breadcrumb-separator"
        role="presentation"
    >
        <slot>
            <ChevronRight />
        </slot>
    </li>
</template>
