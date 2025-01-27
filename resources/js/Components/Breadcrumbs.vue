<script setup>
import Breadcrumb from 'primevue/breadcrumb';
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    items: {
        type: Array,
        required: true
    },
})
</script>

<template>
    <Breadcrumb :model="items" class="mb-6 !ps-0">
        <template #item="{ item }">
            <Link v-if="item.route" :href="item.route">
                <span class="hover:underline">{{ item.label }}</span>
            </Link>
            <span v-else class="font-thin">{{ item.label }}</span>
        </template>
    </Breadcrumb>
</template>
