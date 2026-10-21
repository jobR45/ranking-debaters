<template>
  <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl mb-12">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-white flex items-center gap-2">
        <span>🌍</span> خريطة توزع المناظرين
      </h2>
      <span class="text-xs text-gray-400">انقر على أي موقع لمشاهدة تفاصيل المناظر</span>
    </div>
    
    <div id="debater-map" class="h-96 w-full rounded-xl overflow-hidden border border-gray-800"></div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

const props = defineProps({
  debaters: {
    type: Array,
    required: true
  }
})

onMounted(() => {
  // Initialize map centered around the Middle East/North Africa region
  const map = L.map('debater-map').setView([26.8206, 30.8025], 3)

  // Dark-mode map tile layer
  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://carto.com/">CARTO</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map)

  // Custom marker icon
  const customIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32]
  })

  // Add markers for debaters with valid coordinates
  props.debaters.forEach(debater => {
    if (debater.latitude && debater.longitude) {
      const marker = L.marker([debater.latitude, debater.longitude], { icon: customIcon }).addTo(map)
      
      const popupContent = `
        <div style="font-family: Cairo, sans-serif; text-align: right; color: #111; padding: 4px;">
          <h4 style="margin: 0 0 4px 0; font-weight: bold; font-size: 16px;">${debater.name_ar}</h4>
          <p style="margin: 0 0 6px 0; font-size: 12px; color: #555;">${debater.bio || ''}</p>
          <span style="background: #f59e0b; color: #fff; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 12px;">
            النتيجة: ${debater.official_score?.total_score || 0}/100
          </span>
        </div>
      `
      marker.bindPopup(popupContent)
    }
  })
})
</script>