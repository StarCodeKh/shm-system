<script>
import { h, normalizeClass } from 'vue'
import feather from 'feather-icons'

// "ChevronDownIcon" -> "chevron-down", "BarChart2Icon" -> "bar-chart-2", "XCircleIcon" -> "x-circle" (feather-icons names)
const featherName = icon => String(icon || '')
    .replace(/Icon$/, '')
    .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
    .replace(/([A-Z])([A-Z][a-z])/g, '$1-$2')
    .replace(/([a-zA-Z])([0-9])/g, '$1-$2')
    .toLowerCase()

const escape = value => String(value).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c])

export default {
    name: 'feather-icon',
    props: {
        icon: { required: true },
        svgClasses: { type: [String, Object, Array], default: '' },
        badge: {},
    },
    render() {
        const svgIcon = feather.icons[featherName(this.icon)]
        let html = svgIcon ? svgIcon.toSvg({ class: normalizeClass(this.svgClasses) }) : ''
        if (this.badge) {
            html += `<span class="feather-icon-badge bg-primary text-white h-5 w-5 absolute rounded-full text-xs flex items-center justify-center" style="top: -7px; right: -5px">${escape(this.badge)}</span>`
        }
        return h('span', { class: 'feather-icon select-none relative', innerHTML: html })
    },
}
</script>

<style lang="scss">
@import "@sass/shm/components/featherIcon.scss";
</style>
