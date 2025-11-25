<script setup>
import { computed } from 'vue'
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'

const props = defineProps({
    column: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
})

const sortingState = computed(() => props.column.getIsSorted())

const toggleSorting = (event) => {
    if (!props.column.getCanSort()) {
        return
    }

    props.column.toggleSorting(undefined, event.shiftKey)
}
</script>

<template>
    <Button
        :class="{ 'text-muted-foreground': sortingState === false }"
        :disabled="!column.getCanSort()"
        class="group h-auto w-full justify-start px-2 py-1 text-left font-semibold hover:bg-muted/80"
        type="button"
        variant="ghost"
        @click="toggleSorting"
    >
        <span class="flex-1 truncate">{{ title }}</span>
        <ArrowUpDown
            v-if="sortingState === false"
            class="ml-2 h-4 w-4 text-muted-foreground transition group-hover:text-foreground"
        />
        <ArrowUp
            v-else-if="sortingState === 'asc'"
            class="ml-2 h-4 w-4"
        />
        <ArrowDown
            v-else
            class="ml-2 h-4 w-4"
        />
    </Button>
</template>
