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
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const items = ref([
    {
        label: t('company.name', 2),
        route: '/companies'
    },
    {
        label: t('sector.name', 2),
        route: '/sectors'
    },
    {
        label: t('country.name', 2),
        route: '/countries'
    },
]);
</script>

<template>
    <div class="border-b">
        <div class="px-6 py-1">
            <div class="flex items-center justify-between">
                <h1 class="font-apple-garamond text-5xl">IndexArchive</h1>
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
