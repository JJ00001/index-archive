<script setup>
import LayoutWrapper from "@/Layouts/LayoutWrapper.vue";
import Navbar from "@/Components/Navbar.vue";
import Card from "primevue/card";
</script>

<template>
    <layout-wrapper>
        <div class="space-y-10">
            <navbar class="w-full"/>
            <div class="lg:w-10/12 mx-auto max-w-screen-2xl">
                <Card>
                    <template #content>
                        <slot/>
                    </template>
                </Card>
            </div>
        </div>
    </layout-wrapper>
</template>
