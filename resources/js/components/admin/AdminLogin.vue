<template>
  <div class="min-h-screen bg-gray-950 flex items-center justify-center p-4 rtl" dir="rtl">
    <div class="bg-gray-900 border border-gray-800 rounded-2xl p-8 w-full max-w-md shadow-2xl">
      <h2 class="text-xl font-bold text-amber-400 mb-2 text-center">بوابة دخول المشرفين</h2>
      <p class="text-xs text-gray-400 mb-6 text-center">يرجى تسجيل الدخول بحساب الإدارة للوصول إلى لوحة التحكم</p>

      <form @submit.prevent="handleAdminLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1">البريد الإلكتروني</label>
          <input v-model="form.email" type="email" required class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500" />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-300 mb-1">كلمة المرور</label>
          <input v-model="form.password" type="password" required class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500" />
        </div>

        <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs py-3 rounded-xl transition shadow-lg cursor-pointer">
          تسجيل الدخول للإدارة
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../../store/auth'
import { toast } from 'vue3-toastify'

const router = useRouter()
const auth = useAuth()

const form = ref({
  email: '',
  password: ''
})

const handleAdminLogin = async () => {
  try {
    const res = await fetch('/api/login', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form.value)
    })
    const data = await res.json()

    // Add parentheses around role check so res.ok is required for both
    if (res.ok && (data.user.role === 'admin' || data.user.role === 'super_admin')) {
      auth.setAuth(data.user, data.token)
      toast.success('مرحباً بك في لوحة تحكم المشرفين')
      
      // Push using route name to avoid path mismatches
      router.push({ name: 'AdminOverview' })
    } else {
      toast.error(data.message || 'عفواً، هذا الحساب لا يملك صلاحيات الإدارة.')
    }
  } catch (err) {
    console.error('Login error:', err)
    toast.error('حدث خطأ أثناء تسجيل الدخول.')
  }
}
</script>