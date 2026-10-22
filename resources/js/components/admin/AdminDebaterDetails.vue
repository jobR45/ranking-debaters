<template>
  <div>
    <router-link
      :to="{ name: 'AdminDebaters' }"
      class="mb-6 inline-flex items-center gap-2 text-xs font-semibold text-gray-400 transition hover:text-amber-400"
    >
      <span>→</span>
      العودة إلى قائمة المناظرين
    </router-link>

    <div v-if="isLoading" class="rounded-2xl border border-gray-800 bg-gray-900 p-8 text-center text-sm text-gray-400">
      جارٍ تحميل بيانات المناظر...
    </div>

    <form v-else @submit.prevent="saveDebater" class="space-y-6">
      <section class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-5 flex flex-col gap-5 sm:flex-row sm:items-center">
          <img
            v-if="form.photo_url"
            :src="form.photo_url"
            :alt="form.name_ar || 'صورة المناظر'"
            class="h-24 w-24 rounded-2xl border border-gray-700 object-cover"
          />
          <div
            v-else
            class="flex h-24 w-24 items-center justify-center rounded-2xl border border-gray-700 bg-gray-950 text-3xl font-bold text-amber-400"
          >
            {{ form.name_ar?.charAt(0) || '؟' }}
          </div>
          <div>
            <p class="text-xs text-gray-400">{{ isNew ? 'مناظر جديد' : 'ملف المناظر' }}</p>
            <h1 class="mt-1 text-2xl font-black text-white">{{ form.name_ar || 'بيانات المناظر' }}</h1>
          </div>
          <div class="grid flex-1 grid-cols-2 gap-3 sm:mr-auto sm:max-w-sm">
            <div class="rounded-xl border border-gray-800 bg-gray-950 p-4">
              <span class="block text-xs text-gray-400">النتيجة الرسمية</span>
              <span class="mt-1 block text-xl font-black text-amber-400">{{ scoreTotal }}/100</span>
            </div>
            <div class="rounded-xl border border-gray-800 bg-gray-950 p-4">
              <span class="block text-xs text-gray-400">تقييم الجمهور</span>
              <span class="mt-1 block text-xl font-black text-blue-400">
                {{ audienceScore === null ? 'لا يوجد' : `${audienceScore}%` }}
              </span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
          <label class="space-y-1 text-xs font-semibold text-gray-300">
            الاسم
            <input v-model.trim="form.name_ar" required maxlength="255" class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500" />
          </label>
          <label class="space-y-1 text-xs font-semibold text-gray-300">
            رمز الدولة
            <input v-model.trim="form.country_code" required maxlength="2" class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500" />
          </label>
          <label class="space-y-1 text-xs font-semibold text-gray-300 md:col-span-2">
            رابط الصورة
            <input v-model.trim="form.photo_url" type="url" placeholder="https://..." class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500" />
          </label>
          <label class="space-y-1 text-xs font-semibold text-gray-300 md:col-span-2">
            نبذة عن المناظر
            <textarea v-model="form.bio" rows="3" class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500"></textarea>
          </label>
        </div>
      </section>

      <section class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-5">
          <p class="text-xs font-semibold text-amber-400">التقييم</p>
          <h2 class="mt-1 text-lg font-bold text-white">الدرجات التفصيلية</h2>
          <p class="mt-1 text-xs text-gray-400">حدد درجة من 25 لكل معيار، وسيُحسب المجموع تلقائيًا.</p>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <label v-for="criterion in scoreCriteria" :key="criterion.key" class="space-y-2 text-xs font-semibold text-gray-300">
            <span class="flex items-center justify-between">
              {{ criterion.label }}
              <span class="text-amber-400">{{ form[criterion.key] }}/25</span>
            </span>
            <input
              v-model.number="form[criterion.key]"
              type="number"
              min="0"
              max="25"
              required
              class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500"
            />
          </label>
        </div>
      </section>

      <section class="rounded-2xl border border-gray-800 bg-gray-900 p-6">
        <div class="mb-5">
          <p class="text-xs font-semibold text-amber-400">المحتوى الرسمي</p>
          <h2 class="mt-1 text-lg font-bold text-white">المراجعة المكتوبة</h2>
        </div>

        <div class="space-y-4">
          <label class="block space-y-1 text-xs font-semibold text-gray-300">
            عنوان المراجعة
            <input v-model.trim="form.title" required maxlength="255" class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500" />
          </label>
          <label class="block space-y-1 text-xs font-semibold text-gray-300">
            رابط فيديو المناظرة (اختياري)
            <input v-model.trim="form.video_url" type="url" placeholder="https://..." class="mt-1 block w-full rounded-xl border border-gray-800 bg-gray-950 px-4 py-3 text-xs text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500" />
          </label>

          <div class="space-y-2">
            <span class="block text-xs font-semibold text-gray-300">نص المراجعة</span>
            <div class="flex flex-wrap gap-2 rounded-t-xl border border-gray-800 bg-gray-950 p-3">
              <button v-for="tool in formattingTools" :key="tool.label" type="button" class="rounded-lg border border-gray-700 px-3 py-2 text-xs font-semibold text-gray-200 transition hover:border-amber-500 hover:text-amber-400" @click="formatReview(tool.prefix, tool.suffix, tool.placeholder)">
                {{ tool.label }}
              </button>
            </div>
            <textarea
              ref="reviewTextarea"
              v-model="form.written_review"
              required
              rows="12"
              dir="auto"
              class="w-full rounded-t-none rounded-b-xl border border-gray-800 bg-gray-950 px-4 py-3 font-mono text-xs leading-7 text-white outline-none transition placeholder:text-gray-600 focus:border-amber-500"
              placeholder="اكتب المراجعة، واستخدم أدوات التنسيق لإضافة عناوين وقوائم ونصوص بارزة."
            ></textarea>
            <p class="text-[11px] text-gray-500">يمكنك تنسيق التحديد أو إدراج تنسيق في موضع المؤشر.</p>
          </div>

          <div class="rounded-xl border border-gray-800 bg-gray-950 p-5">
            <p class="mb-3 text-xs font-semibold text-gray-400">معاينة التنسيق</p>
            <WrittenReview :content="form.written_review" />
          </div>
        </div>
      </section>

      <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <router-link :to="{ name: 'AdminDebaters' }" class="rounded-xl border border-gray-700 px-5 py-3 text-center text-xs font-bold text-gray-300 transition hover:bg-gray-800">
          إلغاء
        </router-link>
        <button type="submit" :disabled="isSaving" class="rounded-xl bg-amber-500 px-6 py-3 text-xs font-bold text-gray-950 transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50">
          {{ isSaving ? 'جارٍ الحفظ...' : isNew ? 'إنشاء المناظر ودرجاته ومراجعته' : 'حفظ بيانات المناظر ومراجعته' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue3-toastify'
import WrittenReview from '../WrittenReview.vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  }
})

const router = useRouter()
const isNew = computed(() => props.id === 'new')
const isLoading = ref(false)
const isSaving = ref(false)
const audienceScore = ref(null)
const reviewTextarea = ref(null)
const form = ref(createEmptyForm())
const scoreCriteria = [
  { key: 'etiquette', label: 'آداب النقاش' },
  { key: 'knowledge', label: 'المعرفة والتخصص' },
  { key: 'logic', label: 'المنطق والحجة' },
  { key: 'victory', label: 'الفوز بالمناظرة' }
]
const formattingTools = [
  { label: 'عنوان', prefix: '## ', suffix: '\n', placeholder: 'عنوان فرعي' },
  { label: 'عريض', prefix: '**', suffix: '**', placeholder: 'نص عريض' },
  { label: 'مائل', prefix: '*', suffix: '*', placeholder: 'نص مائل' },
  { label: 'قائمة', prefix: '- ', suffix: '\n', placeholder: 'عنصر في القائمة' },
  { label: 'اقتباس', prefix: '> ', suffix: '\n', placeholder: 'نص مقتبس' }
]
const scoreTotal = computed(() => scoreCriteria.reduce((total, criterion) => total + (Number(form.value[criterion.key]) || 0), 0))

function createEmptyForm() {
  return {
    name_ar: '',
    country_code: 'EG',
    photo_url: '',
    bio: '',
    etiquette: 0,
    knowledge: 0,
    logic: 0,
    victory: 0,
    title: '',
    written_review: '',
    video_url: ''
  }
}

const loadDebater = async () => {
  audienceScore.value = null
  if (isNew.value) {
    form.value = createEmptyForm()
    return
  }

  isLoading.value = true
  const token = localStorage.getItem('token')

  try {
    const response = await fetch(`/api/admin/debaters/${props.id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    if (!response.ok) {
      throw new Error(`Failed to load debater: ${response.status}`)
    }

    const debater = await response.json()
    form.value = {
      ...createEmptyForm(),
      ...debater,
      etiquette: debater.official_score?.etiquette ?? 0,
      knowledge: debater.official_score?.knowledge ?? 0,
      logic: debater.official_score?.logic ?? 0,
      victory: debater.official_score?.victory ?? 0,
      title: debater.debater_review?.title ?? '',
      written_review: debater.debater_review?.written_review ?? '',
      video_url: debater.debater_review?.video_url ?? ''
    }
    audienceScore.value = debater.audience_score
  } catch (error) {
    console.error(error)
    toast.error('تعذر تحميل بيانات المناظر')
    router.replace({ name: 'AdminDebaters' })
  } finally {
    isLoading.value = false
  }
}

const formatReview = async (prefix, suffix, placeholder) => {
  const textarea = reviewTextarea.value
  const start = textarea.selectionStart
  const end = textarea.selectionEnd
  const selectedText = form.value.written_review.slice(start, end) || placeholder
  const insertion = `${prefix}${selectedText}${suffix}`

  form.value.written_review = form.value.written_review.slice(0, start) + insertion + form.value.written_review.slice(end)

  await nextTick()
  textarea.focus()
  textarea.setSelectionRange(start + prefix.length, start + prefix.length + selectedText.length)
}

const saveDebater = async () => {
  isSaving.value = true
  const token = localStorage.getItem('token')
  const responseMethod = isNew.value ? 'POST' : 'PUT'
  const endpoint = isNew.value ? '/api/admin/debaters' : `/api/admin/debaters/${props.id}`

  try {
    const response = await fetch(endpoint, {
      method: responseMethod,
      headers: {
        'Content-Type': 'application/json',
        Authorization: `Bearer ${token}`
      },
      body: JSON.stringify(form.value)
    })
    const result = await response.json()

    if (!response.ok) {
      const validationMessage = Object.values(result.errors || {})[0]?.[0]
      throw new Error(validationMessage || result.message || 'تعذر حفظ بيانات المناظر')
    }

    toast.success(result.message)
    if (isNew.value) {
      router.replace({ name: 'AdminDebaterDetails', params: { id: result.debater.id } })
    } else {
      await loadDebater()
    }
  } catch (error) {
    console.error(error)
    toast.error(error.message || 'تعذر حفظ بيانات المناظر')
  } finally {
    isSaving.value = false
  }
}

watch(() => props.id, loadDebater, { immediate: true })
</script>
