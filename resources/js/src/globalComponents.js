import Vue from 'vue'
import VxTooltip from './layouts/components/vx-tooltip/VxTooltip.vue'
import VxCard from './components/vx-card/VxCard.vue'
import VxList from './components/vx-list/VxList.vue'
import VxBreadcrumb from './layouts/components/VxBreadcrumb.vue'
import FeatherIcon from './components/FeatherIcon.vue'
import DPagination from './components/DPagination.vue'
import VxInputGroup from './components/vx-input-group/VxInputGroup.vue'

Vue.component(VxTooltip.name, VxTooltip)
Vue.component(VxCard.name, VxCard)
Vue.component(VxList.name, VxList)
Vue.component(VxBreadcrumb.name, VxBreadcrumb)
Vue.component(FeatherIcon.name, FeatherIcon)
Vue.component(DPagination.name, DPagination)
Vue.component(VxInputGroup.name, VxInputGroup)

// File field (thumbnail + browse), used by every upload in the app
import DFilePicker from './views/form-builder/DFilePicker.vue'
Vue.component(DFilePicker.name, DFilePicker)

// Tabbed shell for long forms (sections as tabs with error badges)
import DFormTabs from './views/form-builder/DFormTabs.vue'
Vue.component(DFormTabs.name, DFormTabs)

// v-select component
import vSelect from 'vue-select'
// Set the components prop default to return our fresh components
vSelect.props.components.default = () => ({
  Deselect: {
    render: createElement => createElement('feather-icon', {
      props: {
        icon: 'XIcon',
        svgClasses: 'w-4 h-4 mt-1'
      }
    }),
  },
  OpenIndicator: {
    render: createElement => createElement('feather-icon', {
      props: {
        icon: 'ChevronDownIcon',
        svgClasses: 'w-5 h-5'
      }
    }),
  },
});

Vue.component(vSelect)