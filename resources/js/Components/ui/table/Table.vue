<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: null, required: false },
    stickyHeader: { type: Boolean, default: true },
})

const containerStyle = props.stickyHeader ? 'relative w-full' : 'relative w-full overflow-auto'

const tableStyle = props.stickyHeader
    ? 'w-full caption-bottom text-sm [&_th]:sticky [&_th]:top-0 [&_th]:z-10 [&_th]:bg-background'
    : 'w-full caption-bottom text-sm'
</script>

<template>
    <div :class="cn(containerStyle)"
         data-slot="table-container">
        <table
            :class="cn(tableStyle, props.class)"
            data-slot="table"
        >
            <slot />
        </table>
    </div>
</template>
