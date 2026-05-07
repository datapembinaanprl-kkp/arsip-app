cat > resources/js/helpers/geojson.js << 'EOF'
export const CATEGORY_COLORS = {
    'Bangunan'   : '#3b82f6',
    'Jalan'      : '#f59e0b',
    'Sungai'     : '#06b6d4',
    'Hutan'      : '#22c55e',
    'Area'       : '#a855f7',
    'Pariwisata' : '#f97316',
    'Fasilitas'  : '#ec4899',
    'Imported'   : '#6b7280',
    'default'    : '#ef4444',
}

export const getCategoryColor = (kategori) =>
    CATEGORY_COLORS[kategori] ?? CATEGORY_COLORS['default']

export const GEOMETRY_ICONS = {
    Point           : '📍',
    MultiPoint      : '📍',
    LineString      : '〰️',
    MultiLineString : '〰️',
    Polygon         : '⬛',
    MultiPolygon    : '⬛',
}

export const getGeometryIcon = (type) => GEOMETRY_ICONS[type] ?? '🗺️'

export const getLeafletStyle = (feature) => {
    const kategori = feature?.properties?.kategori ?? 'default'
    const geomType = feature?.properties?.geom_type ?? feature?.geometry?.type ?? ''
    const color    = getCategoryColor(kategori)

    const base = { color, weight: 2, opacity: 0.9, fillColor: color, fillOpacity: 0.3 }

    if (geomType.includes('LineString')) {
        return { ...base, fillOpacity: 0, weight: 3 }
    }
    return base
}

export const createPointToLayer = (L) => (feature, latlng) => {
    const color = getCategoryColor(feature?.properties?.kategori)
    return L.circleMarker(latlng, {
        radius: 8, fillColor: color, color: '#ffffff',
        weight: 2, opacity: 1, fillOpacity: 0.9,
    })
}

export const escapeHtml = (str) => {
    if (!str) return ''
    return String(str)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;')
        .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
}

export const buildPopupHtml = (properties) => {
    const { id, nama, kategori, deskripsi, geom_type, centroid, created_at } = properties
    const coords = centroid?.coordinates
        ? `${centroid.coordinates[1].toFixed(6)}, ${centroid.coordinates[0].toFixed(6)}`
        : '—'
    const date = created_at
        ? new Date(created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
        : '—'

    return `
        <div style="font-family:sans-serif;font-size:13px;min-width:220px">
            <div style="background:#f8fafc;padding:10px 12px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;gap:8px">
                <span style="font-size:16px">${getGeometryIcon(geom_type)}</span>
                <strong style="color:#1e293b">${escapeHtml(nama)}</strong>
            </div>
            <div style="padding:10px 12px;display:flex;flex-direction:column;gap:6px">
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#64748b;font-size:11px">Kategori</span>
                    <span style="background:${getCategoryColor(kategori)}20;color:${getCategoryColor(kategori)};padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600">${escapeHtml(kategori)}</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#64748b;font-size:11px">Tipe</span>
                    <span style="color:#334155;font-size:11px">${escapeHtml(geom_type ?? '—')}</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="color:#64748b;font-size:11px">Koordinat</span>
                    <span style="color:#334155;font-size:11px;font-family:monospace">${coords}</span>
                </div>
                ${deskripsi ? `<div style="color:#475569;font-size:11px;border-top:1px solid #f1f5f9;padding-top:6px;margin-top:2px">${escapeHtml(deskripsi)}</div>` : ''}
                <div style="display:flex;justify-content:space-between;border-top:1px solid #f1f5f9;padding-top:6px;margin-top:2px">
                    <span style="color:#94a3b8;font-size:10px">#${id}</span>
                    <span style="color:#94a3b8;font-size:10px">${date}</span>
                </div>
            </div>
        </div>
    `
}

export const getFeatureCenterLatLng = (feature) => {
    const centroid = feature?.properties?.centroid
    if (centroid?.coordinates) return [centroid.coordinates[1], centroid.coordinates[0]]
    return null
}

export const isValidGeoJson = (str) => {
    try {
        const obj = typeof str === 'string' ? JSON.parse(str) : str
        return obj && typeof obj === 'object' && obj.type !== undefined
    } catch { return false }
}
EOF