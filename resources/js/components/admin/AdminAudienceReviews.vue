<template>
  <div>
    <h2 class="text-xl font-bold text-white mb-6">💬 إدارة ومراجعة تقييمات الجمهور</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Select Debater Sidebar -->
      <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl h-fit">
        <label class="block text-xs font-semibold text-gray-300 mb-2">تصفية التقييمات حسب المناظر</label>
        <select v-model="selectedDebaterId" @change="fetchReviews(1)" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
          <option value="">جميع المناظرين</option>
          <option v-for="d in debaters" :key="d.id" :value="d.id">{{ d.name_ar }}</option>
        </select>

        <div v-if="selectedDebaterId" class="mt-4 pt-4 border-t border-gray-800 text-xs text-gray-400 space-y-1">
          <p>عدد التقييمات الحالية: <span class="font-bold text-amber-400">{{ pagination.total }}</span></p>
          <p v-if="averageScore !== null">
            متوسط التقييم:
            <span class="font-bold text-emerald-400">
              {{ averageScore.toFixed(1) }}/100
            </span>
          </p>
        </div>
      </div>

      <!-- Audience Reviews Table Area -->
      <div class="md:col-span-2">
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
          <table class="w-full text-right text-xs">
            <thead class="bg-gray-950 text-gray-400 border-b border-gray-800">
              <tr>
                <th class="p-4" v-if="!selectedDebaterId">المناظر</th>
                <th class="p-4">اسم المقيّم</th>
                <th class="p-4">النتيجة</th>
                <th class="p-4">التعليق</th>
                <th class="p-4 text-center">حذف</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
              <tr v-if="reviews.length === 0">
                <td :colspan="selectedDebaterId ? 4 : 5" class="p-6 text-center text-gray-500">
                  لا توجد تقييمات جمهور لهذا المناظر حتى الآن.
                </td>
              </tr>
              <tr v-for="r in reviews" :key="r.id" class="hover:bg-gray-850/50">
                <td class="p-4 font-bold text-white" v-if="!selectedDebaterId">{{ r.debater?.name_ar || 'غير معروف' }}</td>
                <td class="p-4 text-amber-400">{{ r.reviewer_name }}</td>
                <td class="p-4 font-bold text-emerald-400">{{ r.total_score }}/100</td>
                <td class="p-4 text-gray-300 max-w-xs truncate">{{ r.comment || 'بدون تعليق' }}</td>
                <td class="p-4 text-center">
                  <button @click="deleteReview(r.id)" class="text-red-400 hover:text-red-300 font-semibold px-2 py-1 bg-red-500/10 rounded-lg transition">
                    حذف
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <AdminPagination :pagination="pagination" @change="fetchReviews" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'
import AdminPagination from './AdminPagination.vue'
import { fetchAllPaginatedData } from '../../utils/paginatedData.js'

const debaters = ref([])
const selectedDebaterId = ref('')
const reviews = ref([])
const averageScore = ref(null)
const pagination = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })

const fetchDebaters = async () => {
  const token = localStorage.getItem('token')
  try {
    debaters.value = await fetchAllPaginatedData('/api/admin/debaters', {
      headers: { Authorization: `Bearer ${token}` }
    })
  } catch (error) {
    console.error('Failed to load debaters:', error)
  }
}

const fetchReviews = async (page = 1) => {
  const token = localStorage.getItem('token')
  const params = new URLSearchParams({ page: String(page) })
  if (selectedDebaterId.value) {
    params.set('debater_id', selectedDebaterId.value)
  }

  const res = await fetch(`/api/admin/audience-reviews?${params}`, { headers: { Authorization: `Bearer ${token}` } })
  if (!res.ok) {
    console.error('Failed to load audience reviews:', res.status)
    return
  }

  const data = await res.json()
  if (page > data.last_page && data.last_page > 0) {
    return fetchReviews(data.last_page)
  }

  reviews.value = data.data
  averageScore.value = data.average_score
  pagination.value = data
}

const deleteReview = async (id) => {
  if (!confirm('هل أنت متاكد من حذف هذا التقييم؟')) return
  const token = localStorage.getItem('token')
  const res = await fetch(`/api/admin/audience-reviews/${id}`, {
    method: 'DELETE',
    headers: { Authorization: `Bearer ${token}` }
  })
  if (res.ok) {
    toast.success('تم حذف التقييم بنجاح')
    fetchReviews(pagination.value.current_page)
  } else {
    toast.error('حدث خطأ أثناء حذف التقييم')
  }
}

onMounted(async () => {
  await fetchDebaters()
  await fetchReviews()
})
</script>