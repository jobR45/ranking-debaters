<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold text-white">👥 إدارة المناظرين</h2>
      <button @click="openModal()" class="bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer">
        + إضافة مناظر جديد
      </button>
    </div>

    <!-- Debaters Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
      <table class="w-full text-right text-xs">
        <thead class="bg-gray-950 text-gray-400 border-b border-gray-800">
          <tr>
            <th class="p-4">المناظر</th>
            <th class="p-4">الدولة</th>
            <th class="p-4">النتيجة الرسمية</th>
            <th class="p-4 text-center">الإجراءات</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-for="d in debaters" :key="d.id" class="hover:bg-gray-850/50">
            <td class="p-4 font-bold text-white">{{ d.name_ar }}</td>
            <td class="p-4 text-gray-400">{{ d.country_code }}</td>
            <td class="p-4 font-bold text-amber-400">{{ d.official_score?.total_score || 'غير محدد' }}</td>
            <td class="p-4 text-center space-x-2 space-x-reverse">
              <button @click="openModal(d)" class="text-amber-400 hover:text-amber-300 font-semibold px-2 py-1 bg-amber-500/10 rounded-lg">تعديل</button>
              <button @click="deleteDebater(d.id)" class="text-red-400 hover:text-red-300 font-semibold px-2 py-1 bg-red-500/10 rounded-lg">حذف</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 w-full max-w-lg shadow-2xl relative">
        <button @click="isModalOpen = false" class="absolute top-4 left-4 text-gray-400 hover:text-white">✕</button>
        <h3 class="text-base font-bold text-white mb-4">{{ editingId ? 'تعديل مناظر' : 'إضافة مناظر جديد' }}</h3>

        <form @submit.prevent="saveDebater" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">الاسم باللغة العربية</label>
            <input v-model="form.name_ar" required type="text" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">رمز الدولة (مثال: EG, SA, IQ)</label>
            <input v-model="form.country_code" required type="text" maxlength="2" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">السيرة الذاتية</label>
            <textarea v-model="form.bio" rows="3" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white"></textarea>
          </div>
          <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs py-3 rounded-xl transition">
            حفظ البيانات
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'

const debaters = ref([])
const isModalOpen = ref(false)
const editingId = ref(null)
const form = ref({ name_ar: '', country_code: 'EG', bio: '' })

const fetchDebaters = async () => {
  const token = localStorage.getItem('token')
  const res = await fetch('/api/admin/debaters', { headers: { Authorization: `Bearer ${token}` } })
  if (res.ok) debaters.value = await res.json()
}

const openModal = (debater = null) => {
  if (debater) {
    editingId.value = debater.id
    form.value = { name_ar: debater.name_ar, country_code: debater.country_code, bio: debater.bio }
  } else {
    editingId.value = null
    form.value = { name_ar: '', country_code: 'EG', bio: '' }
  }
  isModalOpen.value = true
}

const saveDebater = async () => {
  const token = localStorage.getItem('token')
  const url = editingId.value ? `/api/admin/debaters/${editingId.value}` : '/api/admin/debaters'
  const method = editingId.value ? 'PUT' : 'POST'

  const res = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify(form.value)
  })

  if (res.ok) {
    toast.success(editingId.value ? 'تم التعديل بنجاح' : 'تمت الإضافة بنجاح')
    isModalOpen.value = false
    fetchDebaters()
  } else {
    toast.error('حدث خطأ أثناء حفظ البيانات')
  }
}

const deleteDebater = async (id) => {
  if (!confirm('هل أنت تأكد من رغبتك في حذف هذا المناظر؟')) return
  const token = localStorage.getItem('token')
  const res = await fetch(`/api/admin/debaters/${id}`, {
    method: 'DELETE',
    headers: { Authorization: `Bearer ${token}` }
  })
  if (res.ok) {
    toast.success('تم الحذف بنجاح')
    fetchDebaters()
  }
}

onMounted(fetchDebaters)
</script>