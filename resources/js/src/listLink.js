// A list opened from a dashboard figure shows exactly the records behind it.
// The dashboard puts the filter in the page URL (/incentive?from=2026-01-01&to=2026-12-31&type=...&label=...),
// the list sends it with its search as `link` (App\Services\ListLink applies it), and DTableList shows `label`
// above the table with a button to clear it.

// The keys the server understands (ListLink::KEYS); anything else in the URL (tab, label, ...) is not a filter
export const LINK_KEYS = ["from", "to", "status", "type", "entity", "registered", "payment"]

// The filter in a route's query, or null when the page was not opened from a figure
export function linkFromRoute(route) {
    const query = (route && route.query) || {}
    const link = {}
    LINK_KEYS.forEach((key) => {
        if (query[key] !== undefined && query[key] !== null && query[key] !== "") link[key] = query[key]
    })
    return Object.keys(link).length ? link : null
}

// The route of a list filtered by `link` (empty values dropped), with `label` describing it for the reader
export function linkRoute(path, link = {}, label = "") {
    const query = {}
    Object.entries(link).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== "" && !(Array.isArray(value) && !value.length)) query[key] = value
    })
    if (!Object.keys(query).length) return path
    if (label) query.label = label
    return { path, query }
}
