cat > resources/js/Components/GIS/LayerControl.vue << 'EOF'
<script setup>
import { ref, watch } from 'vue'
import { getCategoryColor } from '@/helpers/geojson.js'

const props = defineProps({
    map: { type: Object, default: null },
    baseLayers: { type: Object, default: () => ({}) },
    isDarkMode: { type: Boolean, default: false },
    categories: { type: Array, default: () => [] },
    activeCategories: { type: Array, default: () => [] },
})

const emit = defineEmits(['base-layer-change', 'category-toggle', 'update:activeCategories'])

const activeBaseLayer      = ref('OpenStreetMap')
const localActiveCategories = ref([...props.activeCategories])

watch(() => props.isDarkMode, (dark) => {
    if (dark) changeBaseLayer('Dark')
    else changeBaseLayer('OpenStreetMap')
})

const changeBaseLayer = (name) => {
    if (!props.map || !props.baseLayers[name]) return
    Object.values(props.baseLayers).forEach(l => { if (props.map.hasLayer(l)) props.map.removeLayer(l) })
    props.baseLayers[name].addTo(props.map)
    activeBaseLayer.value = name
    emit('base-layer-change', name)
}

const toggleCategory = (kat) => {
    const idx = localActiveCategories.value.indexOf(kat)
    if (idx === -1) localActiveCategories.value.push(kat)
    else localActiveCategories.value.splice(idx, 1)
    emit('update:activeCategories', [...localActiveCategories.value])
    emit('category-toggle', localActiveCategories.value)
}
</script>

<template>
    <div class="absolute top-4 right-4 z-[1000] flex flex-col gap-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-3 w-44">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Base Layer</h4>
            <div class="flex flex-col gap-1">
                <button v-for="(_, name) in baseLayers" :key="name" @click="changeBaseLayer(name)"
                    :class="['w-full text-left px-3 py-1.5 rounded-lg text-xs font-medium transition-all',
                        activeBaseLayer === name ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700']">
                    {{ name }}
                </button>
            </div>
        </div>

        <div v-if="categories.length" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-3 w-44">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori</h4>
            <div class="flex flex-col gap-1">
                <button v-for="kat in categories" :key="kat" @click="toggleCategory(kat)"
                    :class="['flex items-center gap-2 px-2 py-1.5 rounded-lg text-xs transition-all',
                        localActiveCategories.includes(kat) ? 'opacity-100' : 'opacity-40']">
                    <span class="w-2.5 h-2.5 rounded-full flex-shrink-0"
                          :style="{ backgroundColor: getCategoryColor(kat) }" />
                    <span class="text-gray-700 dark:text-gray-300 truncate">{{ kat }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
EOF