cat > resources/js/Components/GIS/MapView.vue << 'EOF'
<script setup>
import { ref, watch, onMounted, shallowRef } from 'vue'
import { useMap } from '@/composables/useMap.js'
import GeoJsonLayer from './GeoJsonLayer.vue'
import LayerControl from './LayerControl.vue'

const props = defineProps({
    geojsonData: { type: Object, default: null },
    selectedFeature: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
    isDarkMode: { type: Boolean, default: false },
    isLoading: { type: Boolean, default: false },
})

const emit = defineEmits(['feature-click'])

const { map, isReady, flyToFeature, fitToLayer, initMap } = useMap('leaflet-map')

const baseLayers        = shallowRef({})
const activeCategories  = ref([])
const filteredGeoJson   = ref(null)

watch(() => props.geojsonData, (data) => {
    if (!data) return filteredGeoJson.value = null
    filteredGeoJson.value = activeCategories.value.length
        ? { ...data, features: data.features.filter(f => activeCategories.value.includes(f.properties?.kategori)) }
        : data
}, { deep: false })

watch(() => props.selectedFeature, (feature) => {
    if (feature && isReady.value) flyToFeature(feature)
})

watch(() => props.categories, (cats) => {
    if (cats.length && !activeCategories.value.length) activeCategories.value = [...cats]
})

onMounted(async () => {
    const result = await initMap()
    if (!result) return
    baseLayers.value = result.baseLayers
    if (props.categories.length) activeCategories.value = [...props.categories]
    if (props.geojsonData) filteredGeoJson.value = props.geojsonData
})

const onLayerReady = (layer) => fitToLayer(layer)

const onCategoryToggle = (cats) => {
    if (!props.geojsonData) return
    filteredGeoJson.value = cats.length
        ? { ...props.geojsonData, features: props.geojsonData.features.filter(f => cats.includes(f.properties?.kategori)) }
        : props.geojsonData
}
</script>

<template>
    <div class="relative w-full h-full">
        <div id="leaflet-map" class="absolute inset-0 z-0" />

        <!-- Loading overlay -->
        <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isLoading || !isReady"
                class="absolute inset-0 z-10 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-blue-500 border-t-transparent rounded-full animate-spin" />
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        {{ !isReady ? 'Memuat peta...' : 'Memuat data...' }}
                    </p>
                </div>
            </div>
        </Transition>

        <GeoJsonLayer v-if="isReady && map" :map="map" :geojson-data="filteredGeoJson"
            :selected-id="selectedFeature?.id ?? selectedFeature?.properties?.id"
            @feature-click="emit('feature-click', $event)" @layer-ready="onLayerReady" />

        <LayerControl v-if="isReady && Object.keys(baseLayers).length" :map="map" :base-layers="baseLayers"
            :is-dark-mode="isDarkMode" :categories="categories"
            v-model:active-categories="activeCategories" @category-toggle="onCategoryToggle" />
    </div>
</template>
EOF