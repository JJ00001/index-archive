<script setup>
const props = defineProps({
    logoPath: {
        type: [String, null],
        required: true,
        default: null
    }
})

const logoPathInStorage = '../storage/' + props.logoPath;
</script>

<template>
    <img v-if="logoPath" :src="logoPathInStorage" class="object-contain" alt=""/>
</template>
