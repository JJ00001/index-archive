<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import Menubar from 'primevue/menubar'

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

const page = usePage();
const currentRoute = page.url;

const isActive = (route) => {
    return currentRoute === route;
}
</script>

<template>
  <Menubar :model="items"
           class="rounded-none! border-t-0! border-x-0! border-b">
        <template #start>
            <span class="font-bold text-2xl mr-10">MSCI World Tracker</span>
        </template>
        <template #item="{ item }">
            <Link :class="{ 'bg-slate-100 rounded-md': isActive(item.route) }" :href="item.route"
                  class="flex items-center px-4 py-2">
                <span :class="item.icon"/>
                <span class="ml-2">{{ item.label }}</span>
            </Link>
        </template>
    </Menubar>
</template>
