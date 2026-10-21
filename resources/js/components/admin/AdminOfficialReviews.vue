<template>
  <div>
    <h2 class="text-xl font-bold text-white mb-6">📝 المراجعات والدرجات الرسمية</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Select Debater -->
      <div class="bg-gray-900 border border-gray-800 p-5 rounded-2xl h-fit">
        <label class="block text-xs font-semibold text-gray-300 mb-2">اختر المناظر لإدارة تقييمه الرسمية</label>
        <select v-model="selectedDebaterId" @change="loadDebaterDetails" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white">
          <option value="" disabled>-- اختر مناظر --</option>
          <option v-for="d in debaters" :key="d.id" :value="d.id">{{ d.name_ar }}</option>
        </select>
      </div>

      <!-- Forms Section -->
      <div v-if="selectedDebaterId" class="md:col-span-2 space-y-6">
        <!-- Official Score Form (25 points per criterion) -->
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl">
          <h3 class="text-base font-bold text-amber-400 mb-4">تعديل النتيجة الرسمية (من 25 لكل معيار)</h3>
          <form @submit.prevent="saveScore" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs text-gray-400 mb-1">آداب النقاش ({{ scoreForm.etiquette }}/25)</label>
              <input v-model.number="scoreForm.etiquette" type="number" min="0" max="25" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">المعرفة ({{ scoreForm.knowledge }}/25)</label>
              <input v-model.number="scoreForm.knowledge" type="number" min="0" max="25" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">المنطق ({{ scoreForm.logic }}/25)</label>
              <input v-model.number="scoreForm.logic" type="number" min="0" max="25" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">الفوز ({{ scoreForm.victory }}/25)</label>
              <input v-model.number="scoreForm.victory" type="number" min="0" max="25" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white" />
            </div>
            <div class="col-span-2 pt-2">
              <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs py-2.5 rounded-xl transition">
                حفظ الدرجات الرسمية
              </button>
            </div>
          </form>
        </div>

        <!-- Official Markdown Review Form -->
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl">
          <h3 class="text-base font-bold text-amber-400 mb-4">كتابة المراجعة الرسمية (Markdown)</h3>
          <form @submit.prevent="saveReview" class="space-y-4">
            <div>
              <label class="block text-xs text-gray-400 mb-1">عنوان المراجعة</label>
              <input v-model="reviewForm.title" required type="text" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">رابط فيديو المناظرة (اختياري)</label>
              <input v-model="reviewForm.video_url" type="url" placeholder="https://youtube.com/..." class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white" />
            </div>
            <div>
              <label class="block text-xs text-gray-400 mb-1">نص المراجعة بالتفصيل (يدعم تنسيق Markdown)</label>
              <textarea v-model="reviewForm.written_review" required rows="6" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2 text-xs text-white font-mono"></textarea>
            </div>
            <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs py-2.5 rounded-xl transition">
              نشر المراجعة الرسمية
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { toast } from 'vue3-toastify'

const debaters = ref([])
const selectedDebaterId = ref('')

const scoreForm = ref({ etiquette: 15, knowledge: 15, logic: 15, victory: 15 })
const reviewForm = ref({ title: '', video_url: '', written_review: '' })

onMounted(async () => {
  const token = localStorage.getItem('token')
  const res = await fetch('/api/admin/debaters', { headers: { Authorization: `Bearer ${token}` } })
  if (res.ok) debaters.value = await res.json()
})

const loadDebaterDetails = async () => {
  const res = await fetch(`/api/debaters/${selectedDebaterId.value}`)
  if (res.ok) {
    const data = await res.json()
    if (data.official_score) {
      scoreForm.value = { ...data.official_score }
    } else {
      scoreForm.value = { etiquette: 15, knowledge: 15, logic: 15, victory: 15 }
    }

    if (data.debater_reviews && data.debater_reviews.length > 0) {
      reviewForm.value = { ...data.debater_reviews[0] }
    } else {
      reviewForm.value = { title: '', video_url: '', written_review: '' }
    }
  }
}

const saveScore = async () => {
  const token = localStorage.getItem('token')
  const res = await fetch(`/api/admin/debaters/${selectedDebaterId.value}/official-score`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify(scoreForm.value)
  })
  if (res.ok) toast.success('تم تحديث النتيجة الرسمية')
}

const saveReview = async () => {
  const token = localStorage.getItem('token')
  const res = await fetch(`/api/admin/debaters/${selectedDebaterId.value}/official-review`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify(reviewForm.value)
  })
  if (res.ok) toast.success('تم نشر المراجعة بنجاح')
}
</script>