<script lang="ts"
        setup>
import {Link} from '@inertiajs/vue3'
import {Card, CardContent, CardHeader, CardTitle} from '@/Components/ui/card'
import type {StatCard} from '@/interfaces/StatCard.ts'

defineProps<{
    stats: StatCard[]
}>()
</script>

<template>
    <div class="flex space-x-5">
        <Card v-for="stat in stats"
              :key="stat.title"
              class="flex-1 gap-4 py-4">
            <CardHeader class="px-4">
                <CardTitle class="whitespace-nowrap text-lg font-medium">{{ stat.title }}</CardTitle>
            </CardHeader>
            <CardContent class="px-4">
                <Link v-if="stat.route"
                      :href="stat.route"
                      :class="[
                          'text-lg underline underline-offset-2',
                          stat.valueClass ?? '',
                      ]">{{ stat.value }}
                </Link>
                <span v-else
                      :class="[
                          'text-lg',
                          stat.valueClass ?? '',
                      ]">{{ stat.value }}</span>
            </CardContent>
        </Card>
    </div>
</template>
