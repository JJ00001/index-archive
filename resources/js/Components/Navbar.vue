<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import Menubar from "primevue/menubar";

const items = ref([
    {
        label: 'Unternehmen',
        icon: 'pi pi-building',
        route: '/companies'
    },
    {
        label: 'Branchen',
        icon: 'pi pi-th-large',
        route: '/sectors'
    },
    {
        label: 'Länder',
        icon: 'pi pi-map',
        route: '/countries'
    },
]);
</script>

<template>
    <Menubar :model="items" class="!rounded-none !border-t-0 !border-x-0 border-b">
        <template #start>
            <span class="font-bold text-2xl mr-10">MSCI World Tracker</span>
        </template>
        <template #item="{ item }">
            <Link class="flex items-center px-4 py-2" :href="item.route">
                <span :class="item.icon"/>
                <span class="ml-2">{{ item.label }}</span>
            </Link>
        </template>
    </Menubar>
</template>
