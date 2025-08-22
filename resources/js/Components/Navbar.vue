<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
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

const page = usePage();
const currentRoute = page.url;

const isActive = (route) => {
    return currentRoute === route;
}
</script>

<template>
    <div class="border-b px-4 py-2">
        <div class="flex items-center">
            <span class="font-bold text-2xl mr-10">MSCI World Tracker</span>
            <NavigationMenu>
                <NavigationMenuList>
                    <NavigationMenuItem v-for="item in items"
                                        :key="item.route">
                        <NavigationMenuLink as-child>
                            <Link
                                :class="[
                                    navigationMenuTriggerStyle(),
                                    'flex items-center gap-2',
                                    {
                                        'bg-primary text-primary-foreground font-medium shadow-sm': isActive(item.route),
                                        'hover:bg-accent hover:text-accent-foreground': !isActive(item.route)
                                    }
                                ]"
                                :href="item.route"
                            >
                                {{ item.label }}
                            </Link>
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </NavigationMenu>
        </div>
    </div>
</template>
