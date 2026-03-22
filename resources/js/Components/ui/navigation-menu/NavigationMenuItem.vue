<script lang="ts"
        setup>
import type {NavigationMenuItemProps} from "reka-ui"
import {NavigationMenuItem} from "reka-ui"
import type {HTMLAttributes} from "vue"
import {reactiveOmit} from "@vueuse/core"
import {cn} from "@/lib/utils"

const props = defineProps<NavigationMenuItemProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")
</script>

<template>
    <NavigationMenuItem
        :class="cn('relative', props.class)"
        v-bind="delegatedProps"
    >
        <slot />
    </NavigationMenuItem>
</template>
