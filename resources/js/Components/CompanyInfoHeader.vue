<script setup>
import CompanyLogo from "@/Components/CompanyLogo.vue";
import { Tag } from "primevue";

const props = defineProps({
    company: {
        type: Object,
        required: true
    },
});

const companyTags = {
    "Ticker": props.company.ticker,
    "ISIN": props.company.isin,
};
</script>

<template>
    <div class="flex">
        <company-logo :logo-path="company.logo" class="w-1/4 max-h-32 mb-auto"/>
        <div>
            <h1 class="text-4xl font-bold">{{ company.name }}</h1>
            <div class="flex space-x-3 mt-2">
                <tag v-for="(value, key) in companyTags" severity="secondary">
                    <template #default>
                        <div class="text-lg">
                            {{ key }} <span class="font-light">{{ value }}</span>
                        </div>
                    </template>
                </tag>
            </div>
        </div>
    </div>
</template>
