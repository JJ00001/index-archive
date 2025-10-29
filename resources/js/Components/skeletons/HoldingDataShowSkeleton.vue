<script setup>
import { Card, CardContent, CardHeader } from '@/Components/ui/card'
import { Skeleton } from '@/Components/ui/skeleton'
</script>

<template>
    <div class="space-y-6">
        <!-- Company Info Header Skeleton -->
        <div>
            <!-- Logo and company info row -->
            <div class="flex space-x-16 mb-4">
                <Skeleton class="h-24 w-24 flex-shrink-0" />
                <div class="flex-1 space-y-3">
                    <Skeleton class="h-9 w-3/4" />
                    <div class="flex space-x-3">
                        <Skeleton class="h-6 w-24" />
                        <Skeleton class="h-6 w-24" />
                    </div>
                </div>
            </div>

            <!-- Stats cards row -->
            <div class="flex space-x-5">
                <Card v-for="i in 3"
                      :key="i"
                      class="flex-1 gap-4 py-4">
                    <CardHeader class="px-4">
                        <Skeleton class="h-5 w-20" />
                    </CardHeader>
                    <CardContent class="px-4">
                        <Skeleton class="h-6 w-32" />
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Weight Chart Card Skeleton -->
        <Card>
            <CardHeader>
                <Skeleton class="h-8 w-32" />
            </CardHeader>
            <CardContent>
                <Skeleton class="h-60 w-full" />
            </CardContent>
        </Card>
    </div>
</template>
