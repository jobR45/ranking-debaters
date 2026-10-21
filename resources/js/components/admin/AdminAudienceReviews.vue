<template>
  <div>
    <h2 class="text-xl font-bold text-white mb-6">💬 إدارة ومراجعة تقييمات الجمهور</h2>

    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
      <table class="w-full text-right text-xs">
        <thead class="bg-gray-950 text-gray-400 border-b border-gray-800">
          <tr>
            <th class="p-4">المناظر</th>
            <th class="p-4">اسم المقيّم</th>
            <th class="p-4">النتيجة</th>
            <th class="p-4">التعليق</th>
            <th class="p-4 text-center">حذف</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-for="r in reviews" :key="r.id" class="hover:bg-gray-850/50">
            <td class="p-4 font-bold text-white">{{ r.debater?.name_ar || 'غير معروف' }}</td>
            <td class="p-4 text-amber-400">{{ r.reviewer_name }}</td>
            <td class="p-4 font-bold text-emerald-400">{{ r.total_score }}/100</td>
            <td class="p-4 text-gray-300 max-w-xs truncate">{{ r.comment || 'بدون تعليق' }}</td>
            <td class="p-4 text-center">
              <button @click="deleteReview(r.id)" class="text-red-400 hover:text-red-300 font-semibold px-2 py-1 bg-red-500/10 rounded-lg">
                حذف
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'

const reviews = ref([])

const fetchReviews = async () => {
  const token = localStorage.getItem('token')
  const res = await fetch('/api/admin/audience-reviews', { headers: { Authorization: `Bearer ${token}` } })
  if (res.ok) reviews.value = await res.json()
}

const deleteReview = async (id) => {
  if (!confirm('هل أنت متاكد من حذف هذا التقييم؟')) return
  const token = localStorage.getItem('token')
  const res = await fetch(`/api/admin/audience-reviews/${id}`, {
    method: 'DELETE',
    headers: { Authorization: `Bearer ${token}` }
  })
  if (res.ok) {
    toast.success('تم حذف التقييم')
    fetchReviews()
  }
}

onMounted(fetchReviews)
</script>