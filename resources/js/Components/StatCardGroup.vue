<script setup>
import { Link } from '@inertiajs/vue3'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'

defineProps({
    stats: {
        type: Array,
        required: true
    }
})
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
                      class="font-data text-lg underline underline-offset-2">{{ stat.value }}
                </Link>
                <span v-else
                      class="font-data text-lg">{{ stat.value }}</span>
            </CardContent>
        </Card>
    </div>
</template>
