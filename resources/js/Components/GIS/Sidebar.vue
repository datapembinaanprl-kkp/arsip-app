<script setup>
import { ref, computed, watch } from 'vue'
import axios from 'axios'
import { getGeometryIcon, getCategoryColor } from '@/helpers/geojson.js'

const props = defineProps({
    categories: { type: Array, default: () => [] },
    selectedId: { type: [Number, null], default: null },
    isOpen: { type: Boolean, default: true },
})

const emit = defineEmits(['select-feature', 'add-feature', 'edit-feature', 'delete-feature', 'import-geojson', 'export-geojson'])

const searchQuery   = ref('')
const selectedKat   = ref('')
const currentPage   = ref(1)
const items         = ref([])
const meta          = ref({})
const isLoading     = ref(false)
let   debounceTimer = null

const fetchList = async () => {
    isLoading.value = true
    try {
        const { data } = await axios.get('/api/spatial-data/list', {
            params: {
                search   : searchQuery.value || undefined,
                kategori : selectedKat.value || undefined,
                page     : currentPage.value,
                per_page : 12,
            },
        })
        items.value = data.data
        meta.value  = data.meta
    } catch (e) {
        console.error('Sidebar fetch error:', e)
    } finally {
        isLoading.value = false
    }
}

watch(searchQuery, () => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => { currentPage.value = 1; fetchList() }, 400)
})
watch(selectedKat, () => { currentPage.value = 1; fetchList() })
watch(currentPage, fetchList)

fetchList()

const totalPages = computed(() => meta.value?.last_page ?? 1)

const confirmDelete = (item) => {
    if (confirm(`Hapus "${item.nama}"?`)) emit('delete-feature', item.id)
}

const refresh = () => { currentPage.value = 1; fetchList() }
defineExpose({ refresh })
</script>

<template>
    <aside :class="['flex flex-col h-full bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 overflow-hidden flex-shrink-0',
        isOpen ? 'w-72' : 'w-0']">

        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <div>
                <h2 class="text-sm font-bold text-gray-900 dark:text-white">Data Spasial</h2>
                <p class="text-xs text-gray-500">{{ meta.total ?? 0 }} record</p>
            </div>
            <button @click="emit('add-feature')"
                class="flex items-center gap-1 px-2.5 py-1.5 bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold rounded-lg transition-colors">
                + Tambah
            </button>
        </div>

        <div class="p-3 border-b border-gray-100 dark:border-gray-800 space-y-2">
            <input v-model="searchQuery" type="text" placeholder="Cari..."
                class="w-full px-3 py-2 text-xs border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <select v-model="selectedKat"
                class="w-full px-3 py-2 text-xs border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Kategori</option>
                <option v-for="kat in categories" :key="kat" :value="kat">{{ kat }}</option>
            </select>
        </div>

        <div class="flex gap-2 px-3 py-2 border-b border-gray-100 dark:border-gray-800">
            <label class="flex-1 flex items-center justify-center gap-1 px-2 py-1.5 text-xs font-medium bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 rounded-lg cursor-pointer hover:bg-emerald-100 transition-colors">
                📥 Import
                <input type="file" accept=".geojson,.json" class="hidden" @change="emit('import-geojson', $event)" />
            </label>
            <button @click="emit('export-geojson')"
                class="flex-1 flex items-center justify-center gap-1 px-2 py-1.5 text-xs font-medium bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-400 rounded-lg hover:bg-violet-100 transition-colors">
                📤 Export
            </button>
        </div>

        <div class="flex-1 overflow-y-auto">
            <div v-if="isLoading" class="p-3 space-y-2">
                <div v-for="i in 5" :key="i" class="h-14 bg-gray-100 dark:bg-gray-800 rounded-xl animate-pulse" />
            </div>

            <div v-else-if="!items.length" class="flex flex-col items-center justify-center h-40 text-gray-400">
                <span class="text-3xl mb-2">🗺️</span>
                <p class="text-xs">Tidak ada data</p>
            </div>

            <div v-else class="p-2 space-y-1">
                <div v-for="item in items" :key="item.id" @click="emit('select-feature', item)"
                    :class="['group flex items-start gap-2.5 p-2.5 rounded-xl cursor-pointer transition-all border',
                        selectedId === item.id
                            ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-300 dark:border-blue-700'
                            : 'border-transparent hover:bg-gray-50 dark:hover:bg-gray-800']">

                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-sm flex-shrink-0 mt-0.5"
                        :style="{ backgroundColor: getCategoryColor(item.kategori) + '20' }">
                        {{ getGeometryIcon(item.geometry_type) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ item.nama }}</p>
                        <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-xs font-medium"
                            :style="{ backgroundColor: getCategoryColor(item.kategori) + '20', color: getCategoryColor(item.kategori) }">
                            {{ item.kategori }}
                        </span>
                    </div>

                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click.stop="emit('edit-feature', item)"
                            class="p-1 rounded text-gray-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30">✏️</button>
                        <button @click.stop="confirmDelete(item)"
                            class="p-1 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30">🗑️</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="totalPages > 1" class="flex items-center justify-between p-3 border-t border-gray-200 dark:border-gray-700">
            <button @click="currentPage--" :disabled="currentPage <= 1"
                class="px-3 py-1 text-xs border border-gray-200 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                ← Prev
            </button>
            <span class="text-xs text-gray-500">{{ currentPage }} / {{ totalPages }}</span>
            <button @click="currentPage++" :disabled="currentPage >= totalPages"
                class="px-3 py-1 text-xs border border-gray-200 dark:border-gray-700 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">
                Next →
            </button>
        </div>
    </aside>
</template>