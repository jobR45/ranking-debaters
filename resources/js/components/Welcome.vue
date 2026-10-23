<template>
  <div class="min-h-screen bg-gray-950 text-white flex flex-col justify-between font-sans" dir="rtl">
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-12 space-y-20 flex-grow w-full">
      
      <!-- Hero Section -->
      <section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <!-- Right Side: Title & Description -->
        <div class="space-y-6">
          <span class="bg-amber-500/10 text-amber-400 border border-amber-500/20 text-xs px-3.5 py-1.5 rounded-full font-semibold">
            المنصة العربية الأولى للمناظرات
          </span>
          <h1 class="text-4xl lg:text-5xl font-black leading-tight">
            نصنع المعيار الأقوى <br/>لتصنيف وتقييم <span class="text-amber-400">المناظرين</span>
          </h1>
          <p class="text-gray-400 text-sm leading-relaxed max-w-lg">
            تابع أحدث المناظرات، واكتشف ترتيب أبرز المناظرين بناءً على لجان تحكيم معتمدة وتقييمات جمهور شفافة ودقيقة.
          </p>
          <div class="flex items-center gap-4 pt-2">
            <router-link to="/ranking" class="bg-amber-500 hover:bg-amber-400 text-gray-950 font-bold px-7 py-3 rounded-xl text-sm transition">
              استعرض التصنيف الآن
            </router-link>
            <router-link to="/how-we-rank" class="border border-gray-800 hover:border-gray-700 text-gray-300 font-medium px-7 py-3 rounded-xl text-sm transition">
              اعرف كيف نقيم
            </router-link>
          </div>
        </div>

        <!-- Left Side: Debater Avatars Grid -->
        <div class="relative flex justify-center">
          <div class="absolute w-72 h-72 bg-amber-500/10 rounded-full blur-3xl -top-10"></div>
          <div class="grid grid-cols-2 gap-4 relative z-10 w-full max-w-md">
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-2xl flex flex-col items-center text-center space-y-3 transform translate-y-6">
              <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=debater1" class="w-16 h-16 rounded-full bg-gray-800 border-2 border-amber-500/50" />
              <div>
                <h3 class="font-bold text-sm text-white">أحمد التميمي</h3>
                <span class="text-xs text-amber-400 font-semibold">المرتبة الأولى</span>
              </div>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-2xl flex flex-col items-center text-center space-y-3">
              <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=debater2" class="w-16 h-16 rounded-full bg-gray-800 border-2 border-gray-700" />
              <div>
                <h3 class="font-bold text-sm text-white">سارة الحارثي</h3>
                <span class="text-xs text-gray-400 font-semibold">المرتبة الثانية</span>
              </div>
            </div>
            <div class="bg-gray-900 border border-gray-800 p-4 rounded-2xl flex flex-col items-center text-center space-y-3 transform -translate-y-4">
              <img src="https://api.dicebear.com/7.x/avataaars/svg?seed=debater3" class="w-16 h-16 rounded-full bg-gray-800 border-2 border-gray-700" />
              <div>
                <h3 class="font-bold text-sm text-white">عمر الفاروق</h3>
                <span class="text-xs text-gray-400 font-semibold">المرتبة الثالثة</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Top Three Debaters Section -->
      <section class="space-y-6 pt-8">
        <div class="flex justify-between items-end">
          <div>
            <h2 class="text-xl font-bold text-white">🏆 قمة المناظرين</h2>
            <p class="text-xs text-gray-400 mt-1">أعلى ثلاثة مناظرين تصنيفاً في المنصة حالياً</p>
          </div>
          <router-link to="/ranking" class="text-xs font-semibold text-amber-400 hover:underline">عرض الكل ←</router-link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div v-for="(debater, index) in topDebaters" :key="debater.id" class="bg-gray-900 border border-gray-800 p-6 rounded-2xl relative overflow-hidden group hover:border-amber-500/50 transition">
            <div class="absolute top-4 left-4 font-black text-2xl text-gray-800 group-hover:text-amber-500/20 transition">#{{ index + 1 }}</div>
            <div class="flex items-center gap-4 mb-4">
              <img :src="debater.avatar || 'https://api.dicebear.com/7.x/avataaars/svg?seed=' + debater.id" class="w-14 h-14 rounded-full bg-gray-800 border border-gray-700 object-cover" />
              <div>
                <h3 class="font-bold text-sm text-white">{{ debater.name_ar }}</h3>
                <span class="text-xs text-amber-400 font-bold">{{ debater.total_score || 0 }} / 100</span>
              </div>
            </div>
            <p class="text-xs text-gray-400 line-clamp-2">{{ debater.bio || 'مناظر معتمد في المنصة يمتلك سجلاً قوياً في المناظرات الفكرية.' }}</p>
          </div>
        </div>
      </section>

      <!-- Latest Debate Review Section -->
      <section class="space-y-6 pt-4">
        <h2 class="text-xl font-bold text-white">📝 أحدث مراجعة وتقييم</h2>
        <div class="bg-gray-900 border border-gray-800 p-6 rounded-2xl space-y-4">
          <div class="flex items-center justify-between">
            <span class="text-xs font-semibold bg-emerald-500/10 text-emerald-400 px-3 py-1 rounded-full">مراجعة رسمية معتمدة</span>
            <span class="text-xs text-gray-500">منذ يومين</span>
          </div>
          <h3 class="text-base font-bold text-white">تقييم مواجهة: السياسات الاقتصادية الحديثة</h3>
          <p class="text-xs text-gray-400 leading-relaxed">
            تميز المناظر بأداء استثنائي في بناء الحجج المنطقية والرد على التفنيدات، مع التزام تام بآداب الحوار وقواعد المناظرة الرسمية...
          </p>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-800 bg-gray-900/30 mt-20">
      <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row items-center justify-between text-xs text-gray-500 gap-4">
        <p>جميع الحقوق محفوظة © 2026 - منصة المناظرين</p>
        <div class="flex gap-6">
          <router-link to="/ranking" class="hover:text-gray-300">التصنيف</router-link>
          <router-link to="/debates" class="hover:text-gray-300">المناظرات</router-link>
          <router-link to="/how-we-rank" class="hover:text-gray-300">كيف نقيم؟</router-link>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const topDebaters = ref([])

onMounted(async () => {
  try {
    const res = await fetch('/api/debaters')
    if (res.ok) {
      const data = await res.json()
      // Sort and take top 3
      topDebaters.value = data.sort((a, b) => (b.total_score || 0) - (a.total_score || 0)).slice(0, 3)
    }
  } catch (err) {
    console.error('Failed to load top debaters', err)
  }
})
</script>