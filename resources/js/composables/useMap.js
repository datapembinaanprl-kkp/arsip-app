cat > resources/js/composables/useMap.js << 'EOF'
import { ref, shallowRef, onBeforeUnmount } from 'vue'

export function useMap(containerId = 'map') {
    const map           = shallowRef(null)
    const isReady       = ref(false)
    const currentZoom   = ref(5)
    const currentCenter = ref([-2.5489, 118.0149])

    const TILE_LAYERS = {
        'OpenStreetMap': {
            url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            options: { attribution: '© OpenStreetMap', maxZoom: 19 },
        },
        'Satellite': {
            url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
            options: { attribution: '© Esri', maxZoom: 18 },
        },
        'Dark': {
            url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
            options: { attribution: '© CARTO', maxZoom: 19 },
        },
        'Terrain': {
            url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
            options: { attribution: '© OpenTopoMap', maxZoom: 17 },
        },
    }

    let L = null

    const initMap = async (options = {}) => {
        L = (await import('leaflet')).default
        await import('leaflet/dist/leaflet.css')

        delete L.Icon.Default.prototype._getIconUrl
        L.Icon.Default.mergeOptions({
            iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon-2x.png',
            iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-shadow.png',
        })

        const container = document.getElementById(containerId)
        if (!container || map.value) return

        map.value = L.map(container, {
            center: currentCenter.value,
            zoom: currentZoom.value,
            zoomControl: false,
            ...options,
        })

        L.control.zoom({ position: 'bottomright' }).addTo(map.value)
        L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(map.value)

        const defaultLayer = L.tileLayer(
            TILE_LAYERS['OpenStreetMap'].url,
            TILE_LAYERS['OpenStreetMap'].options
        ).addTo(map.value)

        map.value.on('zoomend', () => { currentZoom.value = map.value.getZoom() })
        map.value.on('moveend', () => {
            const c = map.value.getCenter()
            currentCenter.value = [c.lat, c.lng]
        })

        isReady.value = true

        const baseLayers = {}
        Object.entries(TILE_LAYERS).forEach(([name, config]) => {
            baseLayers[name] = L.tileLayer(config.url, config.options)
        })

        return { L, baseLayers, defaultLayer }
    }

    const flyTo = (latlng, zoom = 14) => {
        if (!map.value) return
        map.value.flyTo(latlng, zoom, { animate: true, duration: 1.2 })
    }

    const fitToLayer = (geojsonLayer) => {
        if (!map.value || !geojsonLayer) return
        try {
            const bounds = geojsonLayer.getBounds()
            if (bounds.isValid()) map.value.fitBounds(bounds, { padding: [40, 40] })
        } catch (e) { console.warn('fitToLayer error:', e) }
    }

    const flyToFeature = (feature) => {
        const centroid = feature?.properties?.centroid
        if (!centroid?.coordinates) return
        flyTo([centroid.coordinates[1], centroid.coordinates[0]])
    }

    const destroyMap = () => {
        if (map.value) {
            map.value.off()
            map.value.remove()
            map.value = null
            isReady.value = false
        }
    }

    onBeforeUnmount(destroyMap)

    return { map, isReady, currentZoom, currentCenter, TILE_LAYERS, initMap, flyTo, flyToFeature, fitToLayer, destroyMap }
}
EOF