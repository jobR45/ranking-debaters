<template>
    <div
        class="min-h-screen bg-gray-950 text-gray-100 font-['Cairo'] pb-16"
        dir="rtl"
    >
        <div class="max-w-7xl mx-auto px-4 mt-8">
            <!-- Stats Overview Bar -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div
                    class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex items-center justify-between shadow-lg"
                >
                    <div>
                        <p class="text-gray-400 text-sm">إجمالي المناظرين</p>
                        <h3 class="text-3xl font-extrabold text-white mt-1">
                            {{ debaters.length }}
                        </h3>
                    </div>
                    <span class="text-3xl">🗣️</span>
                </div>

                <div
                    class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex items-center justify-between shadow-lg"
                >
                    <div>
                        <p class="text-gray-400 text-sm">المتصدر حالياً</p>
                        <h3 class="text-2xl font-bold text-amber-400 mt-1">
                            {{ topDebater ? topDebater.name_ar : "-" }}
                        </h3>
                    </div>
                    <span class="text-3xl">🥇</span>
                </div>

                <div
                    class="bg-gray-900 border border-gray-800 p-5 rounded-2xl flex items-center justify-between shadow-lg"
                >
                    <div>
                        <p class="text-gray-400 text-sm">حالة التقييمات</p>
                        <h3 class="text-2xl font-bold text-emerald-400 mt-1">
                            مفتوح للجمهور
                        </h3>
                    </div>
                    <span class="text-3xl">⭐</span>
                </div>
            </div>

            <!-- Main Leaderboard Table -->
            <div
                class="bg-gray-900 border border-gray-800 rounded-2xl shadow-xl overflow-hidden mb-12"
            >
                <div
                    class="p-6 border-b border-gray-800 flex justify-between items-center"
                >
                    <h2
                        class="text-2xl font-bold text-white flex items-center gap-2"
                    >
                        <span>📊</span> جدول الترتيب العام
                    </h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right text-gray-300">
                        <thead
                            class="bg-gray-800/60 text-xs text-gray-400 border-b border-gray-800"
                        >
                            <tr>
                                <th class="py-4 px-6">الترتيب</th>
                                <th class="py-4 px-6">المناظر</th>
                                <th class="py-4 px-6 text-center">
                                    النتيجة الرسمية
                                </th>
                                <th class="py-4 px-6 text-center">
                                    تقييم الجمهور
                                </th>
                                <th class="py-4 px-6 text-center">التفاعل</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800/50">
                            <tr
                                v-for="(debater, index) in debaters"
                                :key="debater.id"
                                @click="goToDebater(debater.id)"
                                class="hover:bg-gray-800/60 transition cursor-pointer"
                            >
                                <td
                                    class="py-4 px-6 font-extrabold text-amber-400"
                                >
                                    #{{ index + 1 }}
                                </td>
                                <td
                                    class="py-4 px-6 font-bold text-white text-lg hover:text-amber-400 transition"
                                >
                                    {{ debater.name_ar }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span
                                        class="bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1 rounded-xl font-bold"
                                    >
                                        {{
                                            debater.official_score
                                                ?.total_score || 0
                                        }}/100
                                    </span>
                                </td>
                                <td
                                    class="py-4 px-6 text-center font-bold text-blue-400"
                                >
                                    {{
                                        debater.audience_score
                                            ? debater.audience_score + "%"
                                            : "بدون تقييم"
                                    }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <button
                                        @click.stop="goToDebater(debater.id)"
                                        class="bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold px-4 py-2 rounded-xl text-xs transition"
                                    >
                                        عرض التفاصيل والتقييم ➔
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <DebaterMap v-if="debaters.length > 0" :debaters="debaters" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import DebaterMap from "./DebaterMap.vue";
import { useRouter } from "vue-router";

const router = useRouter();
const debaters = ref([]);

const topDebater = computed(() => debaters.value[0] || null);

const fetchDebaters = async () => {
    try {
        const res = await fetch("/api/debaters");
        debaters.value = await res.json();
    } catch (err) {
        console.error("Error loading debaters:", err);
    }
};

const goToDebater = (id) => {
    router.push({ name: "DebaterProfile", params: { id } });
};

const formatDate = (dateStr) => {
    if (!dateStr) return "";
    return new Date(dateStr).toLocaleDateString("ar-EG");
};

onMounted(() => {
    fetchDebaters();
});
</script>
