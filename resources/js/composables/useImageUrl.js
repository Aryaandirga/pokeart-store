export function imageUrl(path) {
    if (!path) return null
    if (path.startsWith('http')) return path
    if (path.startsWith('uploads/')) return `/${path}`
    return `/storage/${path}`
}