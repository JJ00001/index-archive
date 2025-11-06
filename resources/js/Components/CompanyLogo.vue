<script setup>
import { ref } from 'vue'

const props = defineProps({
    logoPath: {
        type: [String, null],
        default: null,
    },
})

const emit = defineEmits(['hidden'])

const showLogo = ref(!!props.logoPath)

const hideOnError = () => {
    showLogo.value = false
    emit('hidden')
}
</script>

<template>
    <img v-if="showLogo"
         :src="props.logoPath"
         alt=""
         class="w-full h-full object-contain"
         @error="hideOnError" />
</template>
