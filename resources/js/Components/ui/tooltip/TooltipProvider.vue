<script lang="ts"
        setup>
import type {TooltipProviderProps} from 'reka-ui'
import {TooltipProvider, useForwardProps} from 'reka-ui'

const props = defineProps<TooltipProviderProps>()

const forwarded = useForwardProps(props)
</script>

<template>
    <TooltipProvider v-bind="forwarded">
        <slot />
    </TooltipProvider>
</template>
