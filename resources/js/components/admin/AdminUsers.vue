<template>
  <div>
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-xl font-bold text-white">🛡️ إدارة المستخدمين والأدوار</h2>
      <button v-if="auth.state.user?.role === 'super_admin'" @click="isModalOpen = true" class="bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs px-4 py-2.5 rounded-xl transition cursor-pointer">
        + إضافة مسؤول جديد
      </button>
    </div>

    <!-- Users Table -->
    <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
      <table class="w-full text-right text-xs">
        <thead class="bg-gray-950 text-gray-400 border-b border-gray-800">
          <tr>
            <th class="p-4">الاسم</th>
            <th class="p-4">البريد الإلكتروني</th>
            <th class="p-4">الدور الحساب</th>
            <th class="p-4 text-center" v-if="auth.state.user?.role === 'super_admin'">تعديل الدور</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-800">
          <tr v-for="u in users" :key="u.id" class="hover:bg-gray-850/50">
            <td class="p-4 font-bold text-white">{{ u.name }}</td>
            <td class="p-4 text-gray-400">{{ u.email }}</td>
            <td class="p-4">
              <span :class="{
                'bg-purple-500/10 text-purple-400 border-purple-500/20': u.role === 'super_admin',
                'bg-amber-500/10 text-amber-400 border-amber-500/20': u.role === 'admin',
                'bg-gray-800 text-gray-400': u.role === 'user'
              }" class="px-2.5 py-1 rounded-lg border font-semibold">
                {{ u.role === 'super_admin' ? 'Super Admin' : u.role === 'admin' ? 'Admin' : 'مستخدم' }}
              </span>
            </td>
            <td class="p-4 text-center" v-if="auth.state.user?.role === 'super_admin'">
              <select :value="u.role" @change="changeRole(u.id, $event.target.value)" class="bg-gray-950 border border-gray-800 text-gray-300 px-2 py-1 rounded-lg text-xs">
                <option value="user">مستخدم عادي</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Add Admin Modal (Super Admin Only) -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 w-full max-w-md shadow-2xl relative">
        <button @click="isModalOpen = false" class="absolute top-4 left-4 text-gray-400 hover:text-white">✕</button>
        <h3 class="text-base font-bold text-white mb-4">إضافة حساب مسؤول جديد</h3>

        <form @submit.prevent="createAdmin" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">الاسم</label>
            <input v-model="form.name" required type="text" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">البريد الإلكتروني</label>
            <input v-model="form.email" required type="email" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">كلمة المرور</label>
            <input v-model="form.password" required type="password" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white" />
          </div>
          <div>
            <label class="block text-xs font-semibold text-gray-300 mb-1">الدور</label>
            <select v-model="form.role" class="w-full bg-gray-950 border border-gray-800 rounded-xl px-4 py-2.5 text-xs text-white">
              <option value="admin">Admin</option>
              <option value="super_admin">Super Admin</option>
            </select>
          </div>
          <button type="submit" class="w-full bg-amber-500 hover:bg-amber-600 text-gray-950 font-bold text-xs py-3 rounded-xl transition">
            إنشاء الحساب
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuth } from '../../store/auth.js'
import { toast } from 'vue3-toastify'

const auth = useAuth()
const users = ref([])
const isModalOpen = ref(false)
const form = ref({ name: '', email: '', password: '', role: 'admin' })

const fetchUsers = async () => {
  const token = localStorage.getItem('token')
  const res = await fetch('/api/admin/users', { headers: { Authorization: `Bearer ${token}` } })
  if (res.ok) users.value = await res.json()
}

const changeRole = async (userId, newRole) => {
  const token = localStorage.getItem('token')
  const res = await fetch(`/api/admin/users/${userId}/role`, {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify({ role: newRole })
  })

  if (res.ok) {
    toast.success('تم تغيير دور المستخدم بنجاح')
    fetchUsers()
  } else {
    const data = await res.json()
    toast.error(data.message || 'فشل تغيير الدور')
  }
}

const createAdmin = async () => {
  const token = localStorage.getItem('token')
  const res = await fetch('/api/admin/users', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${token}` },
    body: JSON.stringify(form.value)
  })

  if (res.ok) {
    toast.success('تم إضافة المسؤول بنجاح')
    isModalOpen.value = false
    form.value = { name: '', email: '', password: '', role: 'admin' }
    fetchUsers()
  } else {
    const data = await res.json()
    toast.error(data.message || 'حدث خطأ أثناء الإنشاء')
  }
}

onMounted(fetchUsers)
</script>   