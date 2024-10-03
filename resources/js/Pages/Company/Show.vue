<script setup>
defineProps({
    company: {
        type: Object,
        required: true
    }
});
</script>

<template>
    {{ company.name }}
</template>
