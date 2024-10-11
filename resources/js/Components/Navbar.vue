<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import Menubar from "primevue/menubar";
import InputText from "primevue/inputtext";

const items = ref([
    {
        label: 'Unternehmen',
        icon: 'pi pi-building',
        route: '/companies'
    },
    {
        label: 'Branchen',
        icon: 'pi pi-th-large',
        route: '/companies'
    },
    {
        label: 'Länder',
        icon: 'pi pi-map',
        route: '/companies'
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
        <template #end>
            <div class="flex items-center gap-2">
                <InputText placeholder="Suchen" type="text" class="w-32 sm:w-auto"/>
            </div>
        </template>
    </Menubar>
</template>
