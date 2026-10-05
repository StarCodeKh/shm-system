import apiConfig from "@/apiConfig.js";

export default {
    // _Account_Group
    formAttributeAccountGroup: [
        {
            name: "account_type_group_id",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
    // _Account_Type_Group
    formAttributeAccountGroup: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
    // _Account_Type
    formAttributeAccountGroup: [
        {
            name: "account_type_group_id",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
    // Account
    formAttributeAccount: [
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "account_id",
            type: "select",
            options:[],
            required: false
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
    // Item Category
    formAttributeItemCategory: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
    ],
    // Item
    formAttributeItem: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
    ],
    // Price List Type
    formAttributePriceListType: [
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
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
    // Unit
    formAttributeUnit: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
    ],
    // User
    formAttributeUser: [
        {
            name: "entity_id",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "role_id",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "title",
            type: "select",
            required: true,
            options: []
        },

        {
            name: "latin",
            type: "text",
            required: true,
        },
        {
            name: "gender",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "position",
            type: "select",
            required: true,
            options: []
        },
        {
            name: "email",
            type: "text",
            required: true
        },
        {
            name: "password",
            type: "password",
            required: true
        },
        {
            name: "confirm_password",
            type: "password",
            required: true
        },

        {
            name: "telephone_1",
            type: "text",
            required: false
        },
        {
            name: "is_active",
            type: "checkbox",
            required: false
        },
    ],
    // Title
    formAttributeTitle: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        }
    ],
    // Department
    formAttributeDepartment: [
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        }
    ],
    // Entity
    formAttributeEntity: [
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
    // Entity Member
    formAttributeEntityMember: [
        {
            name: "entity_id",
            type: "select",
            required: true,
            options: [
                {
                    "label": "អង្គភាពទី១",
                    "value": 1,
                },
                {
                    "label": "អង្គភាពទី២",
                    "value": 2,
                }
            ],
        },
        {
            name: "fullname",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "description",
            type: "textarea",
            required: false
        },
    ],
    // Position
    formAttributePosition: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        }
    ],
    // CeilingGroup
    formAttributeCeilingGroup: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
    ],
    // CeilingType
    formAttributeCeilingType: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
    ],
    // Objective
    formAttributeObjective: [
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
        {
            name: "select",
            options: "select",
            required: false,
            data: [],//this.data.order_status_choices
        },
    ],
    // Program
    formAttributeProgram: [
        {
            name: "objective_id",
            type: "select",
            required: true,
            hasDefault: false,
            defaultOptions: {},
            options: [
                {
                    value: 1,
                    label: "កម្មវិធីទី១"
                },
                {
                    value: 2,
                    label: "កម្មវិធីទី២"
                }
            ]
        },
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "entity_id",
            type: "select",
            required: true,
            hasFilter: true,
            filterObj: "entity_member_id",
            api: apiConfig._apiMemberByEntity,
            options: [],
        },
        {
            name: "entity_member_id",
            type: "select",
            required: true,
            options: [],
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
        {
            name: "is_active",
            type: "checkbox",
            required: false,
            attributes: [
                {
                    name: "is_active",
                    value: "1"
                }
            ]
        }
    ],
    // Sub Program
    formAttributeSubprogram: [
        {
            name: "program_id",
            type: "select",
            required: true,
            hasDefault: false,
            defaultOptions: {},
            options: [
                {
                    value: 1,
                    label: "កម្មវិធីទី១"
                },
                {
                    value: 2,
                    label: "កម្មវិធីទី២"
                }
            ]
        },
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "entity_id",
            type: "select",
            required: true,
            hasFilter: true,
            filterObj: "entity_member_id",
            api: apiConfig._apiMemberByEntity,
            options: [],
        },
        {
            name: "entity_member_id",
            type: "select",
            required: true,
            options: [],
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
        // {
        //     name: "is_active",
        //     type: "checkbox",
        //     required: false
        // },
        {
            name: "is_active",
            type: "checkbox",
            required: false,
            attributes: [
                {
                    name: "is_active",
                    value: "1"
                }
            ]
        }
    ],
    // Cluster Activity
    formAttributeClusterActivity: [
        {
            name: "sub_program_id",
            type: "select",
            required: true,
            hasDefault: false,
            defaultOptions: {},
            options: [
                {
                    value: 1,
                    label: "កម្មវិធីទី១"
                },
                {
                    value: 2,
                    label: "កម្មវិធីទី២"
                }
            ]
        },
        {
            name: "code",
            type: "text",
            required: true
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "entity_id",
            type: "select",
            required: true,
            hasFilter: true,
            filterObj: "entity_member_id",
            api: apiConfig._apiMemberByEntity,
            options: [],
        },
        {
            name: "entity_member_id",
            type: "select",
            required: true,
            options: [],
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
        // {
        //     name: "is_active",
        //     type: "checkbox",
        //     required: false
        // },
        {
            name: "is_active",
            type: "checkbox",
            required: false,
            attributes: [
                {
                    name: "is_active",
                    value: "1"
                }
            ]
        }
    ],
    // Activity
    formAttributeActivity: [
        {
            name: "program_id",
            type: "select",
            required: true,
            hasFilter: true,
            filterObj: "sub_program_id",
            api: apiConfig._apiSubProgramByPro
        },
        {
            name: "sub_program_id",
            type: "select",
            required: true,
            hasFilter: true,
            filterObj: "cluster_activity_id",
            api: apiConfig._apiEntityBySubPro
        },
        {
            name: "cluster_activity_id",
            type: "select",
            required: true,
            hasFilter: false,
        },
        {
            name: "entity_id",
            type: "select",
            hasFilter: true,
            filterObj: "entity_member_id",
            api: apiConfig._apiMemberByEntity,
            required: true,
            options: [
                {
                    "label": "អង្គភាពទី១",
                    "value": 1,
                },
                {
                    "label": "អង្គភាពទី២",
                    "value": 2,
                }
            ],
        },
        {
            name: "entity_member_id",
            type: "select",
            required: true,
            options: [
                {
                    "label": "បុគ្គលទិ១",
                    "value": 1,
                },
                {
                    "label": "បុគ្គលទិ២",
                    "value": 2,
                }
            ],
        },
        {
            name: "name_en",
            type: "text",
            required: true
        },
        {
            name: "name_kh",
            type: "text",
            required: true
        },
        {
            name: "order_level",
            type: "number",
            required: false
        },
        {
            name: "remark",
            type: "textarea",
            required: false
        },
    ],
    // Task
    formAttributeTask: [
        // {
        //     name: "entity_id",
        //     type: "select",
        //     required: true,
        //     hasFilter: true,
        //     filterObj: "entity_member_id",
        //     api: apiConfig._apiMemberByEntity,
        // },
        // {
        //     name: "program_id",
        //     type: "select",
        //     required: true,
        //     hasFilter: true,
        //     filterObj: "sub_program_id",
        //     api: apiConfig._apiMemberByEntity,
        // },
        // {
        //     name: "program_id",
        //     type: "select",
        //     required: true,
        //     hasFilter: true,
        //     filterObj: "sub_program_id",
        //     api: apiConfig._apiSubProgramByPro
        // },
        // {
        //     name: "sub_program_id",
        //     type: "select",
        //     required: true,
        //     hasFilter: true,
        //     filterObj: "cluster_activity_id",
        //     api: apiConfig._apiEntityBySubPro
        // },
        {
            name: "cluster_activity_id",
            type: "select",
            required: true,
            hasFilter: true,
            filterObj: "activity_id",
            api: apiConfig._apiEntityBySubPro
        },
        {
            name: "activity_id",
            type: "select",
            required: true,
            hasFilter: false,
            // filterObj: "",
        },
        {
            name: "task_id",
            type: "select",
            required: true,
            hasFilter: false,
            // filterObj: "",
        },
        // {
        //     name: "group_chapter",
        //     type: "select",
        //     required: true,
        //     data: [],
        // },
        // {
        //     name: "account_id",
        //     type: "select",
        //     required: true,
        //     data: [],
        // },
        // {
        //     name: "sub_account_id",
        //     type: "select",
        //     required: true,
        //     data: [],
        // },
        {
            name: "name_en",
            type: "text",
            required: true,
            data: [],
        },
        {
            name: "name_kh",
            type: "text",
            required: true,
            data: [],
        },
        // {
        //     name: "lbl_detail",
        //     type: "grid",
        //     flag: "y_n_minus_1",
        //     required: false,
        // },
        // {
        //     name: "unit",
        //     type: "select",
        //     required: false,
        // },
        // {
        //     name: "quantity",
        //     type: "number",
        //     required: false,
        // },
        // {
        //     name: "unit_price",
        //     type: "text",
        //     required: false,
        // },
        // {
        //     name: "time_annual",
        //     type: "number",
        //     required: false,
        // },
        // {
        //     name: "is_reg_exp",
        //     type: "select",
        //     required: false,
        // },
        // {
        //     name: "misc",
        //     type: "textarea",
        //     required: false,
        // }
    ],
}