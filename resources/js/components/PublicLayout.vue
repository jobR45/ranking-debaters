<template>
  <div class="min-h-screen bg-gray-950 text-gray-100" dir="rtl">
    <MainNavigation />
    <router-view />
  </div>
</template>

<script setup>
import MainNavigation from './MainNavigation.vue'
</script>
