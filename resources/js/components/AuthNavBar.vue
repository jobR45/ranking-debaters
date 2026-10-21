<template>
    <div class="flex items-center gap-4">
        <!-- If Logged In -->
        <div
            v-if="auth.isAuthenticated.value"
            class="flex items-center gap-3 bg-gray-900 border border-gray-800 px-4 py-2 rounded-xl shadow-md"
        >
            <img
                v-if="auth.state.user?.avatar"
                :src="auth.state.user.avatar"
                class="w-7 h-7 rounded-full object-cover"
            />
            <div class="text-xs">
                <span class="block font-bold text-white">{{
                    auth.state.user?.name
                }}</span>
                <span class="text-[10px] text-amber-400 font-semibold">{{
                    auth.isAdmin.value ? "مدير المنصة (Admin)" : "مستخدم مسجل"
                }}</span>
            </div>
            <button
                @click="handleLogout"
                class="mr-2 text-xs text-red-400 hover:text-red-300 font-bold px-2 py-1 bg-red-950/40 rounded-lg border border-red-900/50"
            >
                خروج
            </button>
        </div>

        <!-- If Logged Out: Open Login Modal Button -->
        <div v-else>
            <button
                @click="isModalOpen = true"
                class="bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-md flex items-center gap-2 cursor-pointer"
            >
                <span>🔐</span> تسجيل الدخول / إنشاء حساب
            </button>
        </div>
        <!-- Auth Modal Overlay -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        >
            <div
                class="bg-gray-900 border border-gray-800 rounded-2xl p-6 w-full max-w-sm shadow-2xl relative"
            >
                <!-- Close Button -->
                <button
                    @click="isModalOpen = false"
                    class="absolute top-4 left-4 text-gray-400 hover:text-white text-sm"
                >
                    ✕
                </button>

                <h3 class="text-lg font-bold text-white mb-2 text-center">
                    تسجيل الدخول للمنصة
                </h3>
                <p class="text-xs text-gray-400 mb-6 text-center">
                    اختر منصتك المفضلة للمتابعة والتفاعل الفوري
                </p>

                <!-- Vertically Aligned Colored Social Buttons -->
                <div class="flex flex-col gap-3">
                    <a
                        href="/api/auth/google/redirect"
                        class="w-full bg-white hover:bg-gray-100 text-gray-900 font-bold text-xs py-3 px-4 rounded-xl flex items-center justify-center gap-3 transition shadow-md"
                    >
                        <span class="text-base"></span> المتابعة باستخدام Google
                    </a>

                    <a
                        href="/api/auth/discord/redirect"
                        class="w-full bg-[#5865F2] hover:bg-[#4752C4] text-white font-bold text-xs py-3 px-4 rounded-xl flex items-center justify-center gap-3 transition shadow-md"
                    >
                        <span class="text-base"></span> المتابعة باستخدام
                        Discord
                    </a>

                    <a
                        href="/api/auth/tiktok/redirect"
                        class="w-full bg-black border border-gray-800 hover:bg-gray-950 text-white font-bold text-xs py-3 px-4 rounded-xl flex items-center justify-center gap-3 transition shadow-md"
                    >
                        <span class="text-base"></span> المتابعة باستخدام TikTok
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuth } from "../store/auth.js";

const auth = useAuth();
const isModalOpen = ref(false);

const handleLogout = () => {
    auth.logout();
    window.location.reload();
};
</script>
