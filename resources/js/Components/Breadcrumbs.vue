<script setup>
import { Link } from '@inertiajs/vue3'
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
})
</script>

<template>
    <Breadcrumb class="mb-6">
        <BreadcrumbList>
            <template v-for="(item, index) in items"
                      :key="index">
                <BreadcrumbItem>
                    <BreadcrumbLink v-if="item.route"
                                    as-child>
                        <Link :href="item.route">{{ item.label }}</Link>
                    </BreadcrumbLink>
                    <BreadcrumbPage v-else>{{ item.label }}</BreadcrumbPage>
                </BreadcrumbItem>
                <BreadcrumbSeparator v-if="index < items.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
