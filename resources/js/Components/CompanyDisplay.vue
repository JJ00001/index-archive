<script setup>
import CompanyLogo from '@/Components/CompanyLogo.vue'

defineProps({
  company: {
    type: Object,
    required: true,
  },
})
</script>

<template>
  <div class="flex items-center">
    <div class="w-20 h-8 mr-4 shrink-0">
      <CompanyLogo :logo-path="company.logo"
                   class="w-full h-full" />
    </div>
    <div>
      <span class="font-bold">{{ company.name }}</span>
      <br>
      <span class="text-sm text-muted-foreground">{{ company.ticker }}</span>
    </div>
  </div>
</template>