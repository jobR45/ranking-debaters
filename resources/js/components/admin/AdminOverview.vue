<template>
  <div>
    <h2 class="text-xl font-bold text-white mb-6">📊 نظرة عامة علي المنصة</h2>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
      <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl">
        <span class="text-xs text-gray-400 block mb-1">إجمالي المناظرين</span>
        <span class="text-2xl font-black text-amber-400">{{ stats.debatersCount }}</span>
      </div>

      <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl">
        <span class="text-xs text-gray-400 block mb-1">مراجعات المناظرين</span>
        <span class="text-2xl font-black text-blue-400">{{ stats.officialReviewsCount }}</span>
      </div>

      <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl">
        <span class="text-xs text-gray-400 block mb-1">تقييمات الجمهور</span>
        <span class="text-2xl font-black text-emerald-400">{{ stats.audienceReviewsCount }}</span>
      </div>

      <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl">
        <span class="text-xs text-gray-400 block mb-1">المستخدمين المسجلين</span>
        <span class="text-2xl font-black text-purple-400">{{ stats.usersCount }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const stats = ref({
  debatersCount: 0,
  officialReviewsCount: 0,
  audienceReviewsCount: 0,
  usersCount: 0
})

onMounted(async () => {
  const token = localStorage.getItem('token')
  try {
    const [debatersRes, audienceRes, usersRes] = await Promise.all([
      fetch('/api/admin/debaters', { headers: { Authorization: `Bearer ${token}` } }),
      fetch('/api/admin/audience-reviews', { headers: { Authorization: `Bearer ${token}` } }),
      fetch('/api/admin/users', { headers: { Authorization: `Bearer ${token}` } })
    ])

    if (debatersRes.ok) {
      const debaters = await debatersRes.json()
      stats.value.debatersCount = debaters.total
      stats.value.officialReviewsCount = debaters.official_reviews_count
    }
    if (audienceRes.ok) {
      const reviews = await audienceRes.json()
      stats.value.audienceReviewsCount = reviews.total
    }
    if (usersRes.ok) {
      const users = await usersRes.json()
      stats.value.usersCount = users.total
    }
  } catch (err) {
    console.error('Error loading overview stats:', err)
  }
})
</script>