<template>

  <div
            class=" flex justify-between items-center bg-gray-900 border-b border-gray-800 py-1 px-2 w-full md:px-6 md:py-3 shadow-lg"
        >
            <div class="px-6 ">
                <h1 class="text-lg font-black text-amber-400">
                    لوحة تحكم المنصة
                </h1>
                <p class="text-xs text-gray-400 mt-1">
                    إدارة المناظرين والتقييمات والمستخدمين
                </p>
            </div>
            <AuthNavbar />
        </div>
    <div
        class="min-h-screen bg-gray-950 text-gray-100 flex flex-col md:flex-row rtl"
        dir="rtl"
    >
        
        <!-- Sidebar -->
        <aside
            class="w-full md:w-64 bg-gray-900 border-l border-gray-800 flex flex-col"
        >
            <nav class="flex-1 p-4 space-y-2">
                <router-link
                    to="/admin"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-gray-800"
                    active-class="bg-amber-500/10 text-amber-400 border border-amber-500/20"
                >
                    📊 نظرة عامة
                </router-link>
                <router-link
                    to="/admin/debaters"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-gray-800"
                    active-class="bg-amber-500/10 text-amber-400 border border-amber-500/20"
                >
                    👥 إدارة المناظرين
                </router-link>
                <router-link
                    to="/admin/audience-reviews"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-gray-800"
                    active-class="bg-amber-500/10 text-amber-400 border border-amber-500/20"
                >
                    💬 مراجعات الجمهور
                </router-link>
                <router-link
                    to="/admin/users"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs font-bold transition hover:bg-gray-800"
                    active-class="bg-amber-500/10 text-amber-400 border border-amber-500/20"
                >
                    🛡️ إدارة المستخدمين والأدوار
                </router-link>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <router-link
                    to="/"
                    class="block text-center w-full py-2 bg-gray-800 hover:bg-gray-700 text-xs font-bold rounded-xl transition"
                >
                    العودة للموقع الرئيسي ➔
                </router-link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 p-6 md:p-8 overflow-y-auto">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import AuthNavbar from "../AuthNavbar.vue";
</script>
