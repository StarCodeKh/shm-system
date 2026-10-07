// Helpers shared by the app's UI components (ported from Vuesax 3, MIT, so colors and placement match it).

export const color = {
    getColor(colorx, alphax = 1, defaultx = true) {
        if (/^[#]/.test(colorx)) {
            const c = this.hexToRgb(colorx)
            colorx = alphax == 1 ? `rgb(${c.r},${c.g},${c.b})` : `rgba(${c.r},${c.g},${c.b},${alphax})`
        } else if (/^rgba/.test(colorx)) {
            if (colorx.search(/.([0-9]\))$/) == -1 && !defaultx) {
                colorx = colorx.replace(/.?([0-9]\))$/, `${alphax})`)
            }
        } else if (/^(rgb)/.test(colorx)) {
            if (alphax != 1) {
                colorx = colorx.replace(/^(rgb)/, 'rgba').replace(/\)$/, `,${alphax})`)
            }
        }
        return colorx
    },
    isColor(colorx) {
        return ['primary', 'secondary', 'success', 'danger', 'warning', 'dark', 'light'].includes(colorx)
    },
    // Any color (theme name, #hex, rgb) as rgba() with the given opacity
    rColor(colorx, opacity = 1) {
        if (/^[#]/.test(colorx)) {
            const c = this.hexToRgb(colorx)
            colorx = `rgba(${c.r},${c.g},${c.b},${opacity})`
        } else if (/^[rgb]/.test(colorx)) {
            let colorSplit = colorx.split(')')[0]
            colorSplit += /^[rgba]/.test(colorx) ? ')' : `,${opacity})`
            colorx = colorSplit
        }
        if (colorx && /[#()]/.test(colorx)) return colorx
        const name = ['primary', 'success', 'danger', 'warning', 'dark'].includes(colorx) ? colorx : 'primary'
        return `rgba(var(--vs-${name}),${opacity})`
    },
    contrastColor(elementx) {
        let c = elementx
        if (/[#]/g.test(elementx)) {
            const rgbx = this.hexToRgb(elementx)
            c = `rgb(${rgbx.r},${rgbx.g},${rgbx.b})`
        }
        const rgb = c.replace(/^(rgb|rgba)\(/, '').replace(/\)$/, '').replace(/\s/g, '').split(',')
        return (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000 >= 128
    },
    hexToRgb(hex) {
        hex = hex.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i, (m, r, g, b) => r + r + g + g + b + b)
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)
        return result ? { r: parseInt(result[1], 16), g: parseInt(result[2], 16), b: parseInt(result[3], 16) } : null
    },
}

// Overlays (popups, dropdown menus, dialogs) live directly in <body>
export function insertBody(elx, parent) {
    const bodyx = parent || document.body
    bodyx.insertBefore(elx, bodyx.firstChild)
}

// Vue 3 keeps class/style in $attrs when inheritAttrs is false. As in Vue 2, they stay on the
// component's outer element; every other attribute and listener goes to the inner control.
export const splitAttrs = {
    computed: {
        rootAttrs() {
            const { class: cls, style } = this.$attrs
            return { class: cls, style }
        },
        innerAttrs() {
            const { class: _cls, style: _style, ...rest } = this.$attrs
            return rest
        },
    },
}

// A list in this app loads its rows with getData() -> getDataTable() (see views/pages/settings/_Bank.vue)
const isList = vm => vm && typeof vm.getData === 'function' && typeof vm.getDataTable === 'function'

// Reload every list rendered inside this component (Vue 3 has no $children: walk the rendered tree)
export function refreshLists(instance) {
    const visit = (vnode) => {
        if (!vnode) return
        if (vnode.component) {
            const vm = vnode.component.proxy
            if (isList(vm)) return vm.getData()
            return visit(vnode.component.subTree)
        }
        if (Array.isArray(vnode.children)) vnode.children.forEach(child => typeof child === 'object' && visit(child))
        if (vnode.suspense) visit(vnode.suspense.activeBranch)
    }
    if (instance && instance.$) visit(instance.$.subTree)
}
