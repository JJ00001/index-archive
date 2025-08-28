<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
    NavigationMenu,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    navigationMenuTriggerStyle,
} from '@/Components/ui/navigation-menu'

const items = ref([
    {
        label: 'Unternehmen',
        route: '/companies'
    },
    {
        label: 'Branchen',
        route: '/sectors'
    },
    {
        label: 'Länder',
        route: '/countries'
    },
]);
</script>

<template>
    <div class="border-b">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <h1 class="font-apple-garamond text-4xl">MSCI World Tracker</h1>
                <NavigationMenu>
                    <NavigationMenuList class="gap-2">
                        <NavigationMenuItem v-for="item in items"
                                            :key="item.route">
                            <NavigationMenuLink as-child>
                                <Link :class="[navigationMenuTriggerStyle()]"
                                      :href="item.route">
                                    {{ item.label }}
                                </Link>
                            </NavigationMenuLink>
                        </NavigationMenuItem>
                    </NavigationMenuList>
                </NavigationMenu>
            </div>
        </div>
    </div>
</template>
