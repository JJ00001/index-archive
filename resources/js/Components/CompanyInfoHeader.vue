<script setup>
import CompanyLogo from "@/Components/CompanyLogo.vue";
import { Card, Tag } from "primevue";
import { useI18n } from 'vue-i18n';
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    company: {
        type: Object,
        required: true
    },
});

const {n} = useI18n();

const company = props.company;

const companyTags = {
    "Ticker": company.ticker,
    "ISIN": company.isin,
};

const companyStats = {
    "Marktkap.": {
        name: n(company.market_capitalization, 'currencyUSDCompact'),
    },
    "Branche": {
        name: company.sector.name,
        route: route('sectors.show', {sector: company.sector.id})
    },
    "Land": {
        name: company.country.name,
        route: route('countries.show', {country: company.country.id})
    },
    "Börsenplatz": {
        name: company.exchange.name,
    },
}
</script>

<template>
    <div class="flex space-x-16">
        <company-logo :logo-path="company.logo" class="max-w-[20%] max-h-24"/>
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
        <card v-for="(stat, key) in companyStats">
            <template #title>
                {{ key }}
            </template>
            <template #content>
                <Link v-if="stat.route" :href="stat.route" class="underline underline-offset-2">{{ stat.name }}</Link>
                <span v-else>{{ stat.name }}</span>
            </template>
        </card>
    </div>
</template>
