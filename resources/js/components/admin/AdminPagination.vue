<template>
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-gray-400">
    <p>
      عرض {{ pagination.from ?? 0 }} إلى {{ pagination.to ?? 0 }} من {{ pagination.total }}
    </p>

    <div class="flex items-center gap-3">
      <button
        type="button"
        :disabled="pagination.current_page <= 1"
        class="rounded-lg border border-gray-700 px-3 py-2 font-semibold text-gray-200 transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-40"
        @click="$emit('change', pagination.current_page - 1)"
      >
        السابق
      </button>
      <span>صفحة {{ pagination.current_page }} من {{ pagination.last_page }}</span>
      <button
        type="button"
        :disabled="pagination.current_page >= pagination.last_page"
        class="rounded-lg border border-gray-700 px-3 py-2 font-semibold text-gray-200 transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-40"
        @click="$emit('change', pagination.current_page + 1)"
      >
        التالي
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  pagination: {
    type: Object,
    required: true
  }
})

defineEmits(['change'])
</script>
