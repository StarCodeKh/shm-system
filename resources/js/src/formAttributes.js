// Quick-add forms ("+ Add" next to a dropdown, see quickAdd.js) for the role and user fields
export default {
    // Role
    formAttributeRole: [
        {
            name: "entity_id",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "name",
            type: "text",
            required: true
        },
        {
            name: "is_active",
            type: "checkbox",
            required: false
        },
    ],
    // User
    formAttributeUser: [
        { name: "fullname", type: "text", required: true },
        { name: "latin", type: "text", required: true },
        { name: "username", type: "text", required: true },
        { name: "email", type: "text", required: true },
        { name: "telephone_1", type: "text", required: true },
        { name: "role", type: "select", required: true, options: [] },
        { name: "password", type: "password", required: true },
        { name: "password_confirmation", type: "password", required: true },
    ],
}
