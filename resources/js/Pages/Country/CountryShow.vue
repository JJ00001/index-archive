<script setup>
import LayoutMain from "@/Layouts/LayoutMain.vue";
import Card from "primevue/card";
import WeightChart from "@/Components/WeightChart.vue";

const props = defineProps({
    country: {
        type: Object,
        required: true
    },
    weightHistory: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <layout-main>
        <div class="space-y-10">
            <h1 class="text-4xl font-bold">{{ country.name }}</h1>
            <card>
                <template #content>
                    <weight-chart :data="weightHistory"/>
                </template>
            </card>
        </div>
    </layout-main>
</template>
