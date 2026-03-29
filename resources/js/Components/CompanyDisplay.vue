<script lang="ts"
        setup>
import CompanyLogo from '@/Components/CompanyLogo.vue'
import type {Company} from '@/interfaces/Company.ts'

defineProps<{
    company: Company
}>()
</script>

<template>
    <div class="flex items-center">
        <div class="mr-4 h-8 w-16 shrink-0">
            <CompanyLogo
                :logo-path="company.logo"
                class="h-full w-full"
            />
        </div>
        <div>
            <span class="font-bold">{{ company.name }}</span>
            <br>
            <span class="text-sm text-muted-foreground">{{ company.ticker }}</span>
        </div>
    </div>
</template>
