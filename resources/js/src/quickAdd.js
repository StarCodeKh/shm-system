import apiConfig from "@/apiConfig.js";
import formAttr from "@/formAttributes.js";

/**
 * "+ Add" next to a form dropdown: which API a new choice is saved to, by field name.
 *
 * The add form is built from the table's columns (GET /api/v1/lookup/form/{table}; the table is the
 * API's last segment unless `table` says otherwise), so it follows the table without frontend changes.
 * Only fields listed here get the button; to give another dropdown one, add a line.
 * `form` is a fixed form, for tables whose columns aren't what people should fill in (users, roles).
 */
const sources = {
    // Accounting
    account_type_group_id: apiConfig._apiAccountTypeGroup,
    account_type_id: apiConfig._apiAccountType,
    account_group_id: apiConfig._apiAccountGroup,
    account_id: apiConfig._apiAccount,
    sub_account_id: apiConfig._apiAccount,
    bank: apiConfig._apiBank,
    bank_id: apiConfig._apiBank,
    currency: apiConfig._apiCurrency,
    currency_id: apiConfig._apiCurrency,
    from_currency: apiConfig._apiCurrency,
    to_currency: apiConfig._apiCurrency,
    payment_method_id: apiConfig._apiPaymentMethod,
    payment_term: apiConfig._apiPaymentTerm,
    payment_type_id: apiConfig._apiPaymentType,
    request_title: { api: apiConfig._apiPaymentType, form: data => data && data.form_payment_type },
    // Organisation
    department: apiConfig._apiDepartment,
    department_id: apiConfig._apiDepartment,
    entity_id: apiConfig._apiEntity,
    entity_member_id: apiConfig._apiEntityMember,
    title: apiConfig._apiTitle,
    position: apiConfig._apiPosition,
    role_id: { api: apiConfig._apiRole, form: () => formAttr.formAttributeRole },
    user_id: { api: apiConfig._apiUser, form: () => formAttr.formAttributeUser },
    letter_type_id: apiConfig._apiLetterType,
};

/** { api, table, form? } for a field, or null when the field has no "+ Add" */
export function quickAddSource(field) {
    const source = sources[field];
    if (!source) return null;
    const { api, table, form } = typeof source === "string" ? { api: source } : source;
    return { api, form, table: table || api.split("/").pop() };
}

/** The saved row from a store answer ({ data: { data: row } }, { data: row } or the row itself) */
export function savedRecord(answer) {
    let row = answer;
    for (let i = 0; i < 3 && row && typeof row === "object" && !("id" in row) && row.data; i++) row = row.data;
    return row && typeof row === "object" && "id" in row ? row : null;
}

/** The choice in `options` that is this row, whatever the list keys its values by (id, code, name, ...) */
export function optionForRecord(options, row) {
    if (!row || !Array.isArray(options)) return null;
    const keys = ["id", "code", "type_code", "abbreviation", "name_en", "name_kh", "name"];
    for (const key of keys) {
        if (row[key] === undefined || row[key] === null || row[key] === "") continue;
        const option = options.find(o => o && String(o.value) === String(row[key]));
        if (option) return option;
    }
    return null;
}

/** A choice made from the row itself, labelled like the list it joins ("01-Name" when the others show their code) */
export function optionFromRecord(field, row, options = []) {
    const name = row.name_kh || row.name_en || row.name || row.title_kh || row.title || row.code || String(row.id);
    const codeFirst = row.code && options.some(o => o && /^[^\s:-]+[-:]\S/.test(String(o.label)));
    const label = codeFirst && name !== row.code ? `${row.code}-${name}` : name;
    const value = /_id$/.test(field) ? row.id : (row.code || row.name_en || row.name_kh || row.id);
    return { label, value };
}
