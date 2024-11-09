<script setup>
import CompanyLogo from "@/Components/CompanyLogo.vue";

defineProps({
    company: {
        type: Object,
        required: true
    },
});
</script>

<template>
    <div>
        <company-logo :logo-path="company.logo"/>
    </div>
</template>
