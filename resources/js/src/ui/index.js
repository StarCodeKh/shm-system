/*=========================================================================================
    The app's UI components, replacing Vuesax 3 (which only runs on Vue 2).
    They keep Vuesax's tag names (<vs-input>, <vs-popup>, ...), props, events and HTML/class names,
    so the templates, vuesax.css and custom.scss work unchanged. Ported from Vuesax 3 (MIT).
==========================================================================================*/
import VsButton from './VsButton.vue'
import VsCheckbox from './VsCheckbox.vue'
import VsChip from './VsChip.vue'
import VsDialog from './VsDialog.vue'
import VsDivider from './VsDivider.vue'
import VsDropdown from './VsDropdown.vue'
import VsDropdownItem from './VsDropdownItem.vue'
import VsDropdownMenu from './VsDropdownMenu.vue'
import VsIcon from './VsIcon.vue'
import VsInput from './VsInput.vue'
import VsInputNumber from './VsInputNumber.vue'
import VsNavbar from './VsNavbar.vue'
import VsPagination from './VsPagination.vue'
import VsPopup from './VsPopup.vue'
import VsProgress from './VsProgress.vue'
import VsSelect from './VsSelect.vue'
import VsSelectItem from './VsSelectItem.vue'
import VsSidebar from './VsSidebar.vue'
import VsSpacer from './VsSpacer.vue'
import VsSwitch from './VsSwitch.vue'
import VsTab from './VsTab.vue'
import VsTable from './VsTable.vue'
import VsTabs from './VsTabs.vue'
import VsTd from './VsTd.vue'
import VsTextarea from './VsTextarea.vue'
import VsTh from './VsTh.vue'
import VsTr from './VsTr.vue'
import { installVs } from './vs'

const components = {
    VsButton, VsCheckbox, VsChip, VsDivider, VsDropdown, VsDropdownItem, VsDropdownMenu, VsIcon,
    VsInput, VsInputNumber, VsNavbar, VsPagination, VsPopup,
    VsProgress, VsPrompt: VsDialog, VsSelect, VsSelectItem, VsSidebar, VsSpacer, VsSwitch, VsTab,
    VsTable, VsTabs, VsTd, VsTextarea, VsTh, VsTr,
}

export default {
    install(app, options = {}) {
        Object.entries(components).forEach(([name, component]) => app.component(name, component))
        installVs(app, options)
    },
}
