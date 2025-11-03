<script setup>
import { onMounted, ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
    NavigationMenu,
    NavigationMenuContent,
    NavigationMenuItem,
    NavigationMenuLink,
    NavigationMenuList,
    NavigationMenuTrigger,
} from '@/Components/ui/navigation-menu'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const indices = ref([])
const companies = ref([])
const loading = ref(true)

const fetchData = async () => {
    try {
        const [indicesResponse, companiesResponse] = await Promise.all([
            fetch(route('api.indices.top')),
            fetch(route('api.companies.top')),
        ])

        indices.value = await indicesResponse.json()
        companies.value = await companiesResponse.json()
    } catch (error) {
        console.error('Failed to fetch navigation data:', error)
    } finally {
        loading.value = false
    }
}

onMounted(fetchData)
</script>

<template>
    <div class="border-b">
        <div class="max-lg:px-2 px-0 py-1">
            <div class="flex items-center justify-between lg:w-10/12 mx-auto max-w-(--breakpoint-xl)">
                <Link class="hover:opacity-80 transition-opacity"
                      href="/">
                    <h1 class="font-apple-garamond text-5xl max-lg:text-4xl">IndexArchive</h1>
                </Link>
                <NavigationMenu>
                    <NavigationMenuList class="gap-2">
                        <!-- Indices Dropdown -->
                        <NavigationMenuItem>
                            <NavigationMenuTrigger>
                                {{ t('index.name') }}
                            </NavigationMenuTrigger>
                            <NavigationMenuContent>
                                <div class="w-60">
                                    <!-- View All Button -->
                                    <div class="mb-2 pb-2 border-b">
                                        <NavigationMenuLink as-child>
                                            <Link :href="'/indices'"
                                                  class="block select-none rounded-md p-2 text-sm font-medium leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground">
                                                View All {{ t('index.name', 2) }} →
                                            </Link>
                                        </NavigationMenuLink>
                                    </div>
                                    <!-- Individual indices -->
                                    <div v-if="!loading && indices.length > 0">
                                        <NavigationMenuLink v-for="index in indices"
                                                            :key="index.id"
                                                            as-child>
                                            <Link :href="`/indices/${index.id}`"
                                                  class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground">
                                                <div class="text-sm font-medium leading-none">{{ index.name }}</div>
                                            </Link>
                                        </NavigationMenuLink>
                                    </div>
                                </div>
                            </NavigationMenuContent>
                        </NavigationMenuItem>

                        <!-- Companies Dropdown -->
                        <NavigationMenuItem>
                            <NavigationMenuTrigger>
                                {{ t('company.name') }}
                            </NavigationMenuTrigger>
                            <NavigationMenuContent>
                                <div class="w-60">
                                  Coming soon!
                                  <!--                                    &lt;!&ndash; View All Button &ndash;&gt;-->
                                  <!--                                    <div class="mb-2 pb-2 border-b">-->
                                  <!--                                        <NavigationMenuLink as-child>-->
                                  <!--                                            <Link :href="'/companies'"-->
                                  <!--                                                  class="block select-none rounded-md p-2 text-sm font-medium leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground">-->
                                  <!--                                                View All {{ t('company.name', 2) }} →-->
                                  <!--                                            </Link>-->
                                  <!--                                        </NavigationMenuLink>-->
                                  <!--                                    </div>-->
                                  <!--                                    &lt;!&ndash; Individual companies &ndash;&gt;-->
                                  <!--                                    <div v-if="!loading && companies.length > 0">-->
                                  <!--                                        <NavigationMenuLink v-for="company in companies"-->
                                  <!--                                                            :key="company.id"-->
                                  <!--                                                            as-child>-->
                                  <!--                                            <Link :href="`/companies/${company.id}`"-->
                                  <!--                                                  class="block select-none rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground">-->
                                  <!--                                                <div class="text-sm font-medium leading-none">{{ company.name }}</div>-->
                                  <!--                                                <p class="text-sm leading-snug text-muted-foreground">{{-->
                                  <!--                                                        company.ticker-->
                                  <!--                                                    }}</p>-->
                                  <!--                                            </Link>-->
                                  <!--                                        </NavigationMenuLink>-->
                                  <!--                                    </div>-->
                                </div>
                            </NavigationMenuContent>
                        </NavigationMenuItem>
                    </NavigationMenuList>
                </NavigationMenu>
            </div>
        </div>
    </div>
</template>
