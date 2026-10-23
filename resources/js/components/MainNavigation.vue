<template>
  <header class="sticky top-0 z-50 border-b border-gray-800 bg-gray-900/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-3 sm:px-6">
      <router-link to="/" class="flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-xl font-bold text-gray-950">م</span>
        <span class="text-lg font-black tracking-wide text-white">منصة المناظرين</span>
      </router-link>

      <nav class="order-3 flex w-full flex-wrap items-center justify-center gap-x-6 gap-y-2 text-sm font-medium text-gray-300 sm:order-2 sm:w-auto">
        <router-link to="/ranking" class="transition hover:text-amber-400" active-class="text-amber-400">التصنيف</router-link>
        <router-link to="/debates" class="transition hover:text-amber-400" active-class="text-amber-400">المناظرات</router-link>
        <router-link to="/how-we-rank" class="transition hover:text-amber-400" active-class="text-amber-400">كيف نقيم؟</router-link>
      </nav>

      <div class="order-2 sm:order-3">
        <AuthNavbar />
      </div>
    </div>
  </header>
</template>

<script setup>
import AuthNavbar from './AuthNavbar.vue'
</script>
