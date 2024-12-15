<script setup>
import { Link } from "@inertiajs/vue3";
import { Card } from "primevue";

defineProps({
    stats: {
        type: Array,
        required: true
    }
})
</script>

<template>
    <div class="flex space-x-5">
        <card v-for="stat in stats" :key="stat.title">
            <template #title>
                {{ stat.title }}
            </template>
            <template #content>
                <Link v-if="stat.route" :href="stat.route" class="underline underline-offset-2">{{ stat.value }}</Link>
                <span v-else>{{ stat.value }}</span>
            </template>
        </card>
    </div>
</template>
