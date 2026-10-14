import { errorMessage } from "@/axios.js";

/**
 * The standard save of a form, used by every form so they all behave the same:
 *   - every field is checked first; when one is wrong, onInvalid() shows it (a tabbed form jumps to its step)
 *   - one save at a time: vm.saving is true until the server answers (bind it to the button's :disabled)
 *   - the server's field errors (422) go under their fields like the form's own (errors.first(field))
 *   - the answer is shown in the user's language; onSaved(data) runs after a successful save
 *
 *   await saveForm(this, () => axios.put(url, payload), { onInvalid, onSaved })
 */
export async function saveForm(vm, send, { onInvalid, onSaved } = {}) {
    if (vm.saving) return
    if (!(await vm.$validator.validateAll())) {
        if (onInvalid) onInvalid()
        notify(vm, vm.$t("form_check_fields"), "danger")
        return
    }
    vm.saving = true
    vm.$vs.loading()
    try {
        const { data } = await send()
        if (data && data.success === false) {
            notify(vm, data.message || vm.$t("form_save_failed"), "danger")
            return
        }
        notify(vm, (data && data.message) || vm.$t("form_save"), "primary")
        if (onSaved) onSaved(data)
    } catch (error) {
        if (showServerErrors(vm.$validator, error) && onInvalid) onInvalid()
        notify(vm, errorMessage(error, vm.$t("form_save_failed")), "danger")
    } finally {
        vm.saving = false
        vm.$vs.loading.close()
    }
}

/** A form's values as the API takes them: [{ name: value }], a picked choice ({ label, value }) sends its value */
export function formPayload(names, values) {
    return names.map(name => {
        const value = values[name]
        return { [name]: value && typeof value === "object" && !Array.isArray(value) && "value" in value ? value.value : value }
    })
}

/**
 * The server's field errors on a save (422 { errors: { field: [message] } }) shown under their fields,
 * like the form's own checks: errors.has(field) / errors.first(field), and a tabbed form's badges count them.
 * Returns whether there were any.
 */
export function showServerErrors(validator, error) {
    const response = error && error.response
    const errors = response && response.status === 422 && response.data && response.data.errors
    if (!validator || !errors || typeof errors !== "object") return false
    Object.entries(errors).forEach(([field, messages]) => {
        const msg = [].concat(messages)[0]
        if (msg) validator.errors.add({ id: field, field, rule: "server", msg: String(msg) })
    })
    return Object.keys(errors).length > 0
}

/** The server's message for a field, if the last save had one (for a form whose own messages are custom) */
export function serverError(errors, field) {
    const item = errors && errors.items && errors.items.find(error => error.field === field && error.rule === "server")
    return item ? item.msg : ""
}

function notify(vm, text, color) {
    vm.$vs.notify({ title: "Message", text, iconPack: "feather", icon: color === "danger" ? "icon-alert-circle" : "icon-check-circle", color, position: "top-right" })
}
