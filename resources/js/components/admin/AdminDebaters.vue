<template>
  <div>
    <div class="mb-6 flex items-center justify-between gap-4">
      <div>
        <h2 class="text-xl font-bold text-white">إدارة المناظرين</h2>
        <p class="mt-1 text-xs text-gray-400">اختر مناظرًا لإدارة بياناته ودرجاته ومراجعته الرسمية.</p>
      </div>
      <router-link
        :to="{ name: 'AdminDebaterDetails', params: { id: 'new' } }"
        class="shrink-0 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-gray-950 transition hover:bg-amber-600"
      >
        + إضافة مناظر
      </router-link>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-800 bg-gray-900">
      <table class="w-full text-right text-xs">
        <thead class="border-b border-gray-800 bg-gray-950 text-gray-400">
          <tr>
            <th class="p-4">المناظر</th>
            <th class="p-4">الدولة</th>
            <th class="p-4">النتيجة الرسمية</th>
            <th class="p-4">المراجعة الرسمية</th>
            <th class="p-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-if="debaters.length === 0">
            <td colspan="5" class="p-6 text-center text-gray-500">لا يوجد مناظرون بعد.</td>
          </tr>
          <tr
            v-for="debater in debaters"
            :key="debater.id"
            class="transition hover:bg-gray-800/50"
          >
            <td class="p-4 font-bold text-white">
              <router-link
                :to="{ name: 'AdminDebaterDetails', params: { id: debater.id } }"
                class="flex items-center gap-3 hover:text-amber-400"
              >
                <img
                  v-if="debater.photo_url"
                  :src="debater.photo_url"
                  :alt="debater.name_ar"
                  class="h-10 w-10 rounded-full border border-gray-700 object-cover"
                />
                <span
                  v-else
                  class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 text-base text-amber-400"
                >
                  {{ debater.name_ar?.charAt(0) }}
                </span>
                {{ debater.name_ar }}
              </router-link>
            </td>
            <td class="p-4 text-gray-400">{{ debater.country_code || '—' }}</td>
            <td class="p-4 font-bold text-amber-400">
              {{ debater.official_score?.total_score ?? 'غير محدد' }}
            </td>
            <td class="p-4 text-gray-400">
              {{ debater.debater_review ? 'مضافة' : 'غير مضافة' }}
            </td>
            <td class="p-4 text-center">
              <button
                type="button"
                class="rounded-lg bg-red-500/10 px-2 py-1 font-semibold text-red-400 hover:text-red-300"
                @click="deleteDebater(debater.id)"
              >
                حذف
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <AdminPagination :pagination="pagination" @change="fetchDebaters" />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { toast } from 'vue3-toastify'
import AdminPagination from './AdminPagination.vue'

const debaters = ref([])
const pagination = ref({ current_page: 1, last_page: 1, from: 0, to: 0, total: 0 })

const fetchDebaters = async (page = 1) => {
  const token = localStorage.getItem('token')
  const response = await fetch(`/api/admin/debaters?page=${page}`, {
    headers: { Authorization: `Bearer ${token}` }
  })

  if (!response.ok) {
    console.error('Failed to load debaters:', response.status)
    toast.error('تعذر تحميل قائمة المناظرين')
    return
  }

  const data = await response.json()
  if (page > data.last_page && data.last_page > 0) {
    return fetchDebaters(data.last_page)
  }

  debaters.value = data.data
  pagination.value = data
}

const deleteDebater = async (id) => {
  if (!confirm('هل أنت متأكد من رغبتك في حذف هذا المناظر؟')) return

  const token = localStorage.getItem('token')
  const response = await fetch(`/api/admin/debaters/${id}`, {
    method: 'DELETE',
    headers: { Authorization: `Bearer ${token}` }
  })

  if (response.ok) {
    toast.success('تم حذف المناظر بنجاح')
    await fetchDebaters(pagination.value.current_page)
  } else {
    console.error('Failed to delete debater:', response.status)
    toast.error('تعذر حذف المناظر')
  }
}

onMounted(fetchDebaters)
</script>
