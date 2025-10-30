<script setup>
import { Dialog, DialogContent, DialogDescription, DialogTitle } from '@/Components/ui/dialog'
import { VisuallyHidden } from 'reka-ui'
import IndexEntityDialogSkeleton from '@/Components/skeletons/IndexEntityDialogSkeleton.vue'

const props = defineProps({
    state: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
})

const handleOpenUpdate = (value) => {
    if (!value) {
        props.state.close()
    }
}
</script>

<template>
    <Dialog :open="state.isOpen.value"
            @update:open="handleOpenUpdate">
        <DialogContent class="max-w-5xl max-h-[80vh] overflow-y-auto">
            <!-- Required to suppress console warning -->
            <VisuallyHidden>
                <DialogTitle>{{ title }}</DialogTitle>
            </VisuallyHidden>
            <DialogDescription class="hidden" />

            <IndexEntityDialogSkeleton v-if="state.isLoading.value" />

            <div v-else-if="state.error.value"
                 class="space-y-2">
                <p class="font-semibold text-destructive">Something went wrong</p>
                <p class="text-sm text-muted-foreground">
                    {{ state.error.value?.message }}
                </p>
            </div>

            <slot v-else />
        </DialogContent>
    </Dialog>
</template>
