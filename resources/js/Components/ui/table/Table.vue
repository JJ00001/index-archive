<script lang="ts"
        setup>
import type {HTMLAttributes} from "vue"
import {cn} from "@/lib/utils"

const props = withDefaults(defineProps<{
    class?: HTMLAttributes["class"]
    stickyHeader?: boolean
}>(), {
    stickyHeader: true,
})

const containerStyle = props.stickyHeader ? 'relative w-full' : 'relative w-full overflow-auto'

const tableStyle = props.stickyHeader
    ? 'w-full caption-bottom text-sm [&_th]:sticky [&_th]:top-0 [&_th]:z-10 [&_th]:bg-background'
    : 'w-full caption-bottom text-sm'
</script>

<template>
    <div :class="cn(containerStyle)">
        <table :class="cn(tableStyle, props.class)">
            <slot />
        </table>
    </div>
</template>
