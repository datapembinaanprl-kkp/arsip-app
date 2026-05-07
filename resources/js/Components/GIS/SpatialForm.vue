cat > resources/js/Components/GIS/SpatialForm.vue << 'EOF'
<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
    editData: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    isOpen: { type: Boolean, default: false },
})

const emit = defineEmits(['saved', 'close'])

const isSubmitting = ref(false)
const errors       = ref({})
const isEditMode   = computed(() => !!props.editData)

const defaultForm = () => ({ nama: '', kategori: '', deskripsi: '', properties: {}, geometry: '' })
const form = ref(defaultForm())

watch(() => props.editData, (data) => {
    if (data) {
        form.value = {
            nama      : data.nama ?? '',
            kategori  : data.kategori ?? '',
            deskripsi : data.deskripsi ?? '',
            properties: data.properties ?? {},
            geometry  : JSON.stringify(data.geometry, null, 2),
        }
    } else {
        form.value = defaultForm()
    }
    errors.value = {}
}, { immediate: true })

const EXAMPLES = {
    Point      : JSON.stringify({ type: 'Point', coordinates: [106.8456, -6.2088] }, null, 2),
    LineString : JSON.stringify({ type: 'LineString', coordinates: [[106.84, -6.20], [106.85, -6.21]] }, null, 2),
    Polygon    : JSON.stringify({ type: 'Polygon', coordinates: [[[106.84, -6.20], [106.85, -6.20], [106.85, -6.21], [106.84, -6.21], [106.84, -6.20]]] }, null, 2),
}

const submit = async () => {
    errors.value = {}
    isSubmitting.value = true
    try {
        let parsedGeometry
        try { parsedGeometry = JSON.parse(form.value.geometry) }
        catch { errors.value.geometry = 'Format GeoJSON tidak valid.'; return }

        const payload = { ...form.value, geometry: parsedGeometry }

        if (isEditMode.value) {
            await axios.put(`/api/spatial-data/${props.editData.id}`, payload)
        } else {
            await axios.post('/api/spatial-data', payload)
        }

        emit('saved')
        emit('close')
        form.value = defaultForm()
    } catch (e) {
        if (e.response?.status === 422) {
            errors.value = e.response.data.errors ?? {}
            if (e.response.data.message) errors.value._general = e.response.data.message
        } else {
            errors.value._general = 'Terjadi kesalahan server.'
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isOpen" class="fixed inset-0 z-[2000] flex items-center justify-center p-4" @click.self="emit('close')">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" />

                <div class="relative w-full max-w-2xl max-h-[90vh] flex flex-col bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-base font-bold text-gray-900 dark:text-white">
                            {{ isEditMode ? 'Edit Data Spasial' : 'Tambah Data Spasial' }}
                        </h2>
                        <button @click="emit('close')" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">✕</button>
                    </div>

                    <!-- Body -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4">
                        <div v-if="errors._general" class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 rounded-xl text-sm text-red-700">
                            {{ errors._general }}
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama *</label>
                            <input v-model="form.nama" type="text" placeholder="Nama lokasi..."
                                :class="['w-full px-4 py-2.5 text-sm border rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500',
                                    errors.nama ? 'border-red-400' : 'border-gray-300 dark:border-gray-600']" />
                            <p v-if="errors.nama" class="mt-1 text-xs text-red-500">{{ errors.nama[0] }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Kategori *</label>
                            <div class="flex gap-2">
                                <select v-model="form.kategori"
                                    class="flex-1 px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih kategori...</option>
                                    <option v-for="kat in categories" :key="kat" :value="kat">{{ kat }}</option>
                                </select>
                                <input v-model="form.kategori" type="text" placeholder="atau ketik baru"
                                    class="w-36 px-3 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                            <textarea v-model="form.deskripsi" rows="2" placeholder="Deskripsi (opsional)..."
                                class="w-full px-4 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" />
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Geometry (GeoJSON) *</label>
                                <div class="flex gap-1">
                                    <button v-for="type in ['Point', 'LineString', 'Polygon']" :key="type"
                                        @click="form.geometry = EXAMPLES[type]" type="button"
                                        class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                        {{ type }}
                                    </button>
                                </div>
                            </div>
                            <textarea v-model="form.geometry" rows="7" placeholder='{ "type": "Point", "coordinates": [106.84, -6.20] }'
                                :class="['w-full px-4 py-2.5 text-xs font-mono border rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-green-400 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none',
                                    errors.geometry ? 'border-red-400' : 'border-gray-300 dark:border-gray-600']" />
                            <p v-if="errors.geometry" class="mt-1 text-xs text-red-500">
                                {{ Array.isArray(errors.geometry) ? errors.geometry[0] : errors.geometry }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                        <button @click="emit('close')" type="button"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button @click="submit" :disabled="isSubmitting" type="button"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-500 hover:bg-blue-600 disabled:bg-blue-400 disabled:cursor-not-allowed rounded-xl transition-colors flex items-center gap-2">
                            <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            {{ isSubmitting ? 'Menyimpan...' : (isEditMode ? 'Perbarui' : 'Simpan') }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
EOF