import { h } from 'vue'
import VxTooltip from './layouts/components/vx-tooltip/VxTooltip.vue'
import VxCard from './components/vx-card/VxCard.vue'
import VxList from './components/vx-list/VxList.vue'
import VxBreadcrumb from './layouts/components/VxBreadcrumb.vue'
import FeatherIcon from './components/FeatherIcon.vue'
import DPagination from './components/DPagination.vue'
import VxInputGroup from './components/vx-input-group/VxInputGroup.vue'
// File field (thumbnail + browse), used by every upload in the app
import DFilePicker from './views/form-builder/DFilePicker.vue'
// Tabbed shell for long forms (sections as tabs with error badges)
import DFormTabs from './views/form-builder/DFormTabs.vue'
// v-select component
import vSelect from 'vue-select'

// <v-select> draws its clear (x) and open (v) buttons with the app's feather icons
vSelect.props.components.default = () => ({
  Deselect: {
    render: () => h(FeatherIcon, { icon: 'XIcon', svgClasses: 'w-4 h-4 mt-1' }),
  },
  OpenIndicator: {
    render: () => h(FeatherIcon, { icon: 'ChevronDownIcon', svgClasses: 'w-5 h-5' }),
  },
})

export default {
  install(app) {
    app.component(VxTooltip.name, VxTooltip)
    app.component(VxCard.name, VxCard)
    app.component(VxList.name, VxList)
    app.component(VxBreadcrumb.name, VxBreadcrumb)
    app.component(FeatherIcon.name, FeatherIcon)
    app.component(DPagination.name, DPagination)
    app.component(VxInputGroup.name, VxInputGroup)
    app.component(DFilePicker.name, DFilePicker)
    app.component(DFormTabs.name, DFormTabs)
    app.component('v-select', vSelect)
  },
}
