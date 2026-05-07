<script setup>
import { watch, shallowRef, onBeforeUnmount } from 'vue'
import { getLeafletStyle, createPointToLayer, buildPopupHtml } from '@/helpers/geojson.js'

const props = defineProps({
    map: { type: Object, default: null },
    geojsonData: { type: Object, default: null },
    selectedId: { type: [Number, null], default: null },
})

const emit = defineEmits(['feature-click', 'layer-ready'])

const geoJsonLayer = shallowRef(null)
let L = null

watch(() => props.geojsonData, async (newData) => {
    if (!props.map || !newData) return
    await renderLayer(newData)
}, { deep: false })

watch(() => props.selectedId, (id) => highlightFeature(id))

const renderLayer = async (geojsonData) => {
    if (!L) L = (await import('leaflet')).default

    if (geoJsonLayer.value) {
        props.map.removeLayer(geoJsonLayer.value)
        geoJsonLayer.value = null
    }

    if (!geojsonData?.features?.length) return

    geoJsonLayer.value = L.geoJSON(geojsonData, {
        style: getLeafletStyle,
        pointToLayer: createPointToLayer(L),
        onEachFeature: (feature, layer) => {
            layer.bindPopup(buildPopupHtml(feature.properties), {
                maxWidth: 300,
            })
            layer.on('click', () => emit('feature-click', feature))
            layer.on('mouseover', function () {
                if (feature.properties?.id !== props.selectedId) {
                    this.setStyle?.({ weight: 3, fillOpacity: 0.5 })
                }
            })
            layer.on('mouseout', function () {
                if (feature.properties?.id !== props.selectedId) {
                    geoJsonLayer.value?.resetStyle(this)
                }
            })
        },
    }).addTo(props.map)

    emit('layer-ready', geoJsonLayer.value)
}

const highlightFeature = (id) => {
    if (!geoJsonLayer.value) return
    geoJsonLayer.value.eachLayer((layer) => {
        if (layer.feature?.properties?.id === id) {
            layer.setStyle?.({ weight: 4, fillOpacity: 0.6, color: '#f59e0b' })
            layer.openPopup?.()
        } else {
            geoJsonLayer.value?.resetStyle(layer)
        }
    })
}

onBeforeUnmount(() => {
    if (geoJsonLayer.value && props.map) props.map.removeLayer(geoJsonLayer.value)
})

defineExpose({ geoJsonLayer })
</script>

<template><slot /></template>