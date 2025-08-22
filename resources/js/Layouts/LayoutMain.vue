<script setup>
import LayoutWrapper from '@/Layouts/LayoutWrapper.vue'
import Navbar from '@/Components/Navbar.vue'
</script>

<template>
    <layout-wrapper>
        <div class="space-y-10">
            <navbar class="w-full"/>
          <div class="lg:w-10/12 mx-auto max-w-(--breakpoint-xl)">
                <slot/>
            </div>
        </div>
    </layout-wrapper>
</template>
