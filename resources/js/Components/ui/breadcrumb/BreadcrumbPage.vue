<script setup>
import { cn } from '@/lib/utils'

const props = defineProps({
    class: { type: null, required: false },
})
</script>

<template>
  <span
      :class="cn('text-foreground font-normal', props.class)"
      aria-current="page"
      aria-disabled="true"
      data-slot="breadcrumb-page"
      role="link"
  >
    <slot />
  </span>
</template>
