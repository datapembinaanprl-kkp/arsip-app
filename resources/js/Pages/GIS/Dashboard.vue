cat > resources/js/Pages/GIS/Dashboard.vue << 'EOF'
<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'
import MapView from '@/Components/GIS/MapView.vue'
import Sidebar from '@/Components/GIS/Sidebar.vue'
import SpatialForm from '@/Components/GIS/SpatialForm.vue'

const props = defineProps({
    kategoriList: { type: Array, default: () => [] },
})

const isDarkMode      = ref(false)
const sidebarOpen     = ref(true)
const isLoadingMap    = ref(true)
const geojsonData     = ref(null)
const selectedFeature = ref(null)
const categories      = ref([...props.kategoriList])
const formOpen        = ref(false)
const editTarget      = ref(null)
const sidebarRef      = ref(null)
const notification    = ref(null)

onMounted(() => {
    isDarkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    applyDarkMode(isDarkMode.value)
    fetchGeoJson()
})

const applyDarkMode = (dark) => document.documentElement.classList.toggle('dark', dark)
const toggleDarkMode = () => { isDarkMode.value = !isDarkMode.value; applyDarkMode(isDarkMode.value) }

const fetchGeoJson = async () => {
    isLoadingMap.value = true
    try {
        const { data } = await axios.get('/api/spatial-data')
        geojsonData.value = data
        const cats = [...new Set(data.features.map(f => f.properties?.kategori).filter(Boolean))]
        categories.value = cats
    } catch (e) {
        showNotification('Gagal memuat data GeoJSON.', 'error')
    } finally {
        isLoadingMap.value = false
    }
}

const onFeatureClick    = (feature) => { selectedFeature.value = feature }
const onSelectFromSidebar = (item) => { selectedFeature.value = item }
const onAddFeature      = () => { editTarget.value = null; formOpen.value = true }
const onEditFeature     = (item) => { editTarget.value = item; formOpen.value = true }

const onDeleteFeature = async (id) => {
    try {
        await axios.delete(`/api/spatial-data/${id}`)
        showNotification('Data berhasil dihapus.', 'success')
        afterMutation()
    } catch { showNotification('Gagal menghapus data.', 'error') }
}

const onFormSaved = () => {
    showNotification(editTarget.value ? 'Data berhasil diperbarui.' : 'Data berhasil ditambahkan.', 'success')
    afterMutation()
}

const afterMutation = () => { fetchGeoJson(); sidebarRef.value?.refresh() }

const onImportGeoJson = async (event) => {
    const file = event.target.files?.[0]
    if (!file) return
    const reader = new FileReader()
    reader.onload = async (e) => {
        try {
            const geojson = JSON.parse(e.target.result)
            const { data } = await axios.post('/api/spatial-data/import', { geojson })
            showNotification(data.message, 'success')
            afterMutation()
        } catch (err) {
            showNotification(err.response?.data?.message ?? 'Gagal import.', 'error')
        }
    }
    reader.readAsText(file)
    event.target.value = ''
}

const onExportGeoJson = async () => {
    try {
        const { data } = await axios.get('/api/spatial-data/export')
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/geo+json' })
        const url  = URL.createObjectURL(blob)
        const a    = Object.assign(document.createElement('a'), { href: url, download: `gis-export-${Date.now()}.geojson` })
        a.click()
        URL.revokeObjectURL(url)
        showNotification('Export berhasil.', 'success')
    } catch { showNotification('Gagal export.', 'error') }
}

const showNotification = (message, type = 'info') => {
    notification.value = { message, type }
    setTimeout(() => notification.value = null, 4000)
}
</script>

<template>
    <Head title="GIS Dashboard" />

    <div class="h-screen flex flex-col overflow-hidden bg-gray-100 dark:bg-gray-950">
        <!-- Navbar -->
        <header class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 shadow-sm z-50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    ☰
                </button>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white text-sm">🗺️</div>
                    <div>
                        <h1 class="text-sm font-bold text-gray-900 dark:text-white leading-none">GIS Dashboard</h1>
                        <p class="text-xs text-gray-500">PostGIS + Leaflet</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-semibold rounded-full">
                    {{ geojsonData?.features?.length ?? 0 }} Features
                </span>
                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold rounded-full">
                    {{ categories.length }} Kategori
                </span>
                <button @click="fetchGeoJson" :class="['p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors', isLoadingMap && 'animate-spin']" title="Refresh">🔄</button>
                <button @click="toggleDarkMode" class="p-2 rounded-xl text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors" :title="isDarkMode ? 'Light mode' : 'Dark mode'">
                    {{ isDarkMode ? '☀️' : '🌙' }}
                </button>
            </div>
        </header>

        <!-- Main -->
        <div class="flex flex-1 overflow-hidden">
            <Sidebar ref="sidebarRef" :categories="categories"
                :selected-id="selectedFeature?.id ?? selectedFeature?.properties?.id"
                :is-open="sidebarOpen"
                @select-feature="onSelectFromSidebar"
                @add-feature="onAddFeature"
                @edit-feature="onEditFeature"
                @delete-feature="onDeleteFeature"
                @import-geojson="onImportGeoJson"
                @export-geojson="onExportGeoJson" />

            <main class="flex-1 relative overflow-hidden">
                <MapView :geojson-data="geojsonData" :selected-feature="selectedFeature"
                    :categories="categories" :is-dark-mode="isDarkMode" :is-loading="isLoadingMap"
                    @feature-click="onFeatureClick" />
            </main>
        </div>

        <!-- Toast -->
        <Transition enter-active-class="transition ease-out duration-300" enter-from-class="translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100" leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100" leave-to-class="translate-y-4 opacity-0">
            <div v-if="notification"
                :class="['fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] px-5 py-3 rounded-2xl shadow-xl text-sm font-semibold border',
                    notification.type === 'success'
                        ? 'bg-emerald-50 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-700'
                        : 'bg-red-50 dark:bg-red-900 text-red-800 dark:text-red-200 border-red-200 dark:border-red-700']">
                {{ notification.type === 'success' ? '✅' : '❌' }} {{ notification.message }}
            </div>
        </Transition>

        <SpatialForm :is-open="formOpen" :edit-data="editTarget" :categories="categories"
            @saved="onFormSaved" @close="formOpen = false" />
    </div>
</template>
EOF