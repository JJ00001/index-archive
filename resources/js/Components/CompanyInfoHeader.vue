<script setup>
import CompanyLogo from "@/Components/CompanyLogo.vue";
import { Card, Tag } from "primevue";
import { useI18n } from 'vue-i18n';

const props = defineProps({
    company: {
        type: Object,
        required: true
    },
});

const {n} = useI18n();

const companyTags = {
    "Ticker": props.company.ticker,
    "ISIN": props.company.isin,
};

const companyStats = {
    "Marktkap.": n(props.company.market_capitalization, 'currencyUSDCompact'),
    "Branche": props.company.sector.name,
    "Land": props.company.country.name,
    "Börsenplatz": props.company.exchange.name,
}
</script>

<template>
    <div class="flex space-x-10">
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
    <div class="flex space-x-5">
        <card v-for="(value, key) in companyStats">
            <template #title>
                {{ key }}
            </template>
            <template #content>
                {{ value }}
            </template>
        </card>
    </div>
</template>
