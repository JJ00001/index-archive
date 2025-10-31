<script setup>
import { Card, CardContent, CardHeader } from '@/Components/ui/card'
import { Skeleton } from '@/Components/ui/skeleton'
</script>

<template>
    <div class="space-y-6">
        <div class="space-y-3">
            <Skeleton class="h-8 w-1/2" />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <Card class="py-4">
                    <CardHeader class="px-4">
                        <Skeleton class="h-5 w-24" />
                    </CardHeader>
                    <CardContent class="px-4">
                        <Skeleton class="h-6 w-28" />
                    </CardContent>
                </Card>
                <Card class="py-4">
                    <CardHeader class="px-4">
                        <Skeleton class="h-5 w-20" />
                    </CardHeader>
                    <CardContent class="px-4">
                        <Skeleton class="h-6 w-32" />
                    </CardContent>
                </Card>
            </div>
        </div>
        <Card>
            <CardHeader>
                <Skeleton class="h-6 w-24" />
            </CardHeader>
            <CardContent>
                <Skeleton class="h-60 w-full" />
            </CardContent>
        </Card>
    </div>
</template>
