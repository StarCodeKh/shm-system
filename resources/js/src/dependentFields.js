import axios from "@/axios.js";

/**
 * Dropdowns whose choices depend on another dropdown in the same form: child field -> parent field.
 *
 * Any form with both fields (DForm, DSearch, or a page that calls watchDependents) gets this without page config:
 * the child lists only the parent's choices (GET /api/v1/lookup/{child}?{parent}={value}, see LookupController),
 * stays empty until a parent is picked, and a picked child that doesn't belong to the new parent is cleared.
 * To link another pair, add a line here and the filter for it in LookupController::lookups().
 */
const parents = {
    department_id: "entity_id", // a department (នាយកដ្ឋាន) belongs to one unit (អង្គភាព)
};

export function parentField(field) {
    return parents[field] || null;
}

/** The value of a picked choice ({ label, value }) or of a plain value */
export function chosenValue(chosen) {
    return chosen && typeof chosen === "object" ? chosen.value : chosen;
}

function isEmpty(value) {
    return value === null || value === undefined || value === "";
}

// Shared by every form for the session, per language and parent value
const requests = {};

/** The child's choices for one parent value ([] when no parent is picked) */
export function dependentChoices(field, parentValue, locale) {
    const parent = parents[field];
    if (!parent || isEmpty(parentValue)) return Promise.resolve([]);
    const key = `${locale}:${field}:${parentValue}`;
    if (!(key in requests)) {
        requests[key] = axios.get(`/api/v1/lookup/${field}`, { params: { [parent]: parentValue } })
            .then(({ data }) => (Array.isArray(data) ? data : []))
            .catch(() => {
                delete requests[key]; // try again next time
                return [];
            });
    }
    return requests[key];
}

/** Forget a child's cached lists, so the next load sees a newly added choice */
export function forgetDependentChoices(field) {
    Object.keys(requests).filter(key => key.includes(`:${field}:`)).forEach(key => delete requests[key]);
}

/**
 * Keeps the dependent dropdowns among `fields` in step with their parents.
 * `values()` returns the object holding the picked values (e.g. form.attribute);
 * each child's list goes to vm.dependentOptions[child], so forms sharing one `data` don't change each other's lists.
 * Safe to call again when the fields change: a pair is watched once.
 */
export function watchDependents(vm, fields, values) {
    const names = (fields || []).map(field => (field && typeof field === "object" ? field.name : field));
    vm._dependentWatchers = vm._dependentWatchers || {};
    names.forEach(child => {
        const parent = parents[child];
        if (!parent || !names.includes(parent) || vm._dependentWatchers[child]) return;
        vm._dependentWatchers[child] = vm.$watch(() => chosenValue((values() || {})[parent]), async value => {
            const choices = await dependentChoices(child, value, vm.$i18n.locale);
            const current = values() || {};
            if (chosenValue(current[parent]) !== value) return; // the parent changed again meanwhile
            vm.dependentOptions[child] = choices;
            const picked = chosenValue(current[child]);
            if (!isEmpty(picked)) current[child] = choices.find(o => o && String(o.value) === String(picked)) || null;
        }, { immediate: true });
    });
}
