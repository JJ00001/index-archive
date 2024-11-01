<script setup>
const props = defineProps({
    company: {
        type: Object,
        required: true
    },
});

const logoInStorage = '../storage/' + props.company.logo;
</script>

<template>
    <div>
        <img v-if="company.logo" :src="logoInStorage" alt="logo"/>
    </div>
</template>
