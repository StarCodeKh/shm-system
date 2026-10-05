import Vue from 'vue'
import Router from 'vue-router'
import auth from "@/auth/authService";
import firebase from 'firebase/app'
import 'firebase/auth'

Vue.use(Router)

const router = new Router({
    mode: 'history',
    base: process.env.BASE_URL,
    scrollBehavior () {
        return { x: 0, y: 0 }
    },
    routes: [
        {
            // =============================================================================
            // MAIN LAYOUT ROUTES
            // =============================================================================
            path: '',
            component: () => import('./layouts/main/Main.vue'),
            children: [
                // Components

                {
                    path: '/components/tabs',
                    name: 'component-tabs',
                    component: () => import('@/views/components/vuesax/tabs/Tabs.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'Components' },
                            { title: 'Tabs', active: true },
                        ],
                        pageTitle: 'Tabs',
                        rule: 'editor'
                    },
                },
            // =============================================================================
            // Theme Routes
            // =============================================================================
                    {
                        path: '/',
                        redirect: '/dashboard'
                    },
                    {
                        path: '/dashboard',
                        name: 'dashboard',
                        component: () => import('./views/Dashboard.vue'),
                        meta: {
                            rule: 'editor',
                        }
                    },
                    // {
                    //     path: '/dashboard',
                    //     name: 'dashboard-ecommerce',
                    //     component: () => import('./views/DashboardECommerce.vue'),
                    //     meta: {
                    //         rule: 'admin'
                    //     }
                    // },
                // =============================================================================
                // User
                // =============================================================================
                {
                    path: '/user',
                    name: 'data-user-list',
                    component: () => import('@/views/pages/settings/_User.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'អ្នកប្រើប្រាស់'},
                            { title: 'រាយនាមអ្នកប្រើប្រាស់', active: true },
                        ],
                        pageTitle: 'អ្នកប្រើប្រាស់',
                        rule: 'editor'
                    },
                },
                {
                path: '/user/view/:userId',
                    name: 'user-view',
                    component: () => import('@/views/pages/users/user-edit/UserSettings.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'អ្នកប្រើប្រាស់', url: '/user'},
                            { title: 'View', active: true },
                        ],
                        // pageTitle: 'User View',
                        rule: 'editor'
                    },
                },
                {
                    path: '/user/edit/:userId',
                    name: 'user-edit',
                    component: () => import('@/views/pages/users/user-edit/UserSettings.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'អ្នកប្រើប្រាស់', url: '/user'},
                            { title: 'ទិន្នន័យអ្នកប្រើប្រាស់', active: true },
                        ],
                        // pageTitle: 'User View',
                        rule: 'editor'
                    },
                },
                {
                    path: '/auditrail',
                    name: 'data-auditrail',
                    component: () => import('@/views/pages/settings/_Audit.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'កំណត់ហេតុ'},
                            { title: 'កំណត់ហេតុប្រើប្រាស់', active: true },
                        ],
                        pageTitle: 'កំណត់ហេតុ',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Policy Form
                // =============================================================================
                {
                    path: '/policy/list',
                    name: 'PolicyRequestList',
                    component: () => import('@/views/pages/policy-form/List.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីសំណើរ'},
                            { title: 'រាយនាមបញ្ជីសំណើរ', active: true },
                        ],
                        pageTitle: 'បញ្ជីសំណើរ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/policy/form',
                    name: 'PolicyRequestForm',
                    component: () => import('@/views/pages/policy-form/form/FormWizard.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីសំណើរ'},
                            { title: 'រាយនាមបញ្ជីសំណើរ', active: true },
                        ],
                        pageTitle: 'អ្នកប្រើប្រាស់',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Role
                // =============================================================================
                {
                    path: '/role',
                    name: 'data-role-list',
                    component: () => import('@/views/pages/settings/_Role.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'សិទ្ធិ'},
                            { title: 'រាយឈ្មោះសិទ្ធិ', active: true },
                        ],
                        pageTitle: 'សិទ្ធិ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/role/form/:roleId',
                    name: 'data-role-form',
                    component: () => import('@/views/pages/roles/RoleForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'សិទ្ធិ', url: '/role/list' },
                            { title: 'រាយឈ្មោះសិទ្ធិ', active: true },
                        ],
                        pageTitle: 'សិទ្ធិ',
                        rule: 'editor'
                    },
                },
                 {
                    path: '/role/menu/form/:roleId',
                    name: 'data-role-menu-form',
                    component: () => import('@/views/pages/roles/RoleForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'សិទ្ធិ', url: '/role/list' },
                            { title: 'រាយឈ្មោះសិទ្ធិ', active: true },
                        ],
                        pageTitle: 'សិទ្ធិ',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Program Management
                // =============================================================================
                {
                    path: 'module/program-arrangement',
                    name: 'ProgramManagement',
                    component: () => import('./views/pages/modules/program-management/Index.vue'),
                    meta: {
                        pageTitle: 'គ្រប់គ្រងកម្មវិធី',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'គ្រប់គ្រងកម្មវិធី', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                {
                    path: '/module/program-arrangement/objective',
                    name: "objective",
                    component: () => import('./views/pages/modules/program-management/_Objective.vue'),
                    meta: {
                        pageTitle: 'គោលបំណង',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'គោលបំណង', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                {
                    path: '/module/program-arrangement/program',
                    slug: 'program',
                    name: "program",
                    icon: "UserIcon",
                    i18n: "Program",
                    component: () => import('./views/pages/modules/program-management/_Program.vue'),
                    meta: {
                        pageTitle: 'កម្មវិធី',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'កម្មវិធី', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                {
                    path: '/module/program-arrangement/subprogram',
                    slug: 'subprogram',
                    name: "subprogram",
                    icon: "UserIcon",
                    i18n: "ProgramSubProgram",
                    component: () => import('./views/pages/modules/program-management/_SubProgram.vue'),
                    meta: {
                        pageTitle: 'អនុកម្មវិធី',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'អនុកម្មវិធី', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                {
                    path: '/module/program-arrangement/cluster-activity',
                    slug: 'cluster-activity',
                    name: "cluster-activity",
                    icon: "UserIcon",
                    i18n: "ProgramClusterActivity",
                    component: () => import('./views/pages/modules/program-management/_ClusterActivity.vue'),
                    meta: {
                        pageTitle: 'ចង្កោមសកម្មភាព',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'ចង្កោមសកម្មភាព', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                {
                    path: '/module/program-arrangement/activity',
                    slug: 'activity',
                    name: "activity",
                    icon: "UserIcon",
                    i18n: "ProgramActivity",
                    component: () => import('./views/pages/modules/program-management/_Activity.vue'),
                    meta: {
                        pageTitle: 'សកម្មភាព',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'សកម្មភាព', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                {
                    path: '/module/program-arrangement/task',
                    slug: 'task',
                    name: "task",
                    icon: "UserIcon",
                    i18n: "ProgramTask",
                    component: () => import('./views/pages/modules/program-management/_Task.vue'),
                    meta: {
                        pageTitle: 'ការងារ',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ផ្ទាំងគ្រប់គ្រង' },
                            { title: 'ការងារ', active: true },
                        ],
                        rule: 'editor',
                    }
                },
                // =============================================================================
                // Budget Arrangement
                // =============================================================================
                {           
                    path: 'module/budget-arrangement/credit-revenue-rule',
                    name: 'CreditIncomeRule',
                    component: () => import('@/views/pages/modules/budget-arrangement/credit-revenue-rule/Index.vue'),
                    meta: {
                        pageTitle: 'ឥណទានច្បាប់ចំណូល',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'ឥណទានច្បាប់ចំណូល', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/credit-expense-rule',
                    name: 'CreditExpenseRule',
                    component: () => import('@/views/pages/modules/budget-arrangement/credit-expense-rule/Index.vue'),
                    meta: {
                        pageTitle: 'ឥណទានច្បាប់ចំណាយ',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'ឥណទានច្បាប់ចំណាយ', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/budget-ceiling',
                    name: 'Ceiling',
                    component: () => import('@/views/pages/modules/budget-arrangement/budget-ceiling/Index.vue'),
                    meta: {
                        pageTitle: 'ពិដានថវិកា',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'ពិដានថវិកា', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/budget-ceiling/list',
                    name: 'BudgetCeilingList',
                    component: () => import('@/views/pages/modules/budget-arrangement/budget-ceiling/List.vue'),
                    meta: {
                        pageTitle: 'ពិដានថវិកា',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'កំណត់ពិដានថវិកា', url: '/module/budget-arrangement/budget-ceiling' },
                            { title: 'ពិដានថវិកា', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/pip',
                    name: 'PIP',
                    component: () => import('@/views/pages/modules/budget-arrangement/pip/Index.vue'),
                    meta: {
                        pageTitle: 'រៀបចំគម្រោងវិនិយោគសាធារណៈ',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'រៀបចំគម្រោងវិនិយោគសាធារណៈ', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/bsp',
                    name: 'BPS',
                    component: () => import('@/views/pages/modules/budget-arrangement/bsp/Index.vue'),
                    meta: {
                        pageTitle: 'រៀបចំយុទ្ធសាស្ត្រថវិកា',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'រៀបចំយុទ្ធសាស្ត្រថវិកា', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/pb',
                    name: 'PB',
                    component: () => import('@/views/pages/modules/budget-arrangement/pb/Index.vue'),
                    meta: {
                        pageTitle: 'រៀបចំថវិកា',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'គម្រោងវិនិយោគថវិកា', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                // =============================================================================
                // Budget Implementing Monitoring
                // =============================================================================
                {           
                    path: 'module/budget-monitoring/income-arrangement',
                    name: 'IncomeArrangement',
                    component: () => import('@/views/pages/modules/budget-monitoring/income-expense-arrangement/_income_program.vue'),
                    meta: {
                        pageTitle: 'រៀបចំកម្មវិធីចំណូល',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'តាមដានអនុវត្តថវិកា', url: '/' },
                            { title: 'រៀបចំកម្មវិធីចំណូល', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-monitoring/expense-arrangement',
                    name: 'ExpenseArrangement',
                    component: () => import('@/views/pages/modules/budget-monitoring/income-expense-arrangement/_expense_program.vue'),
                    meta: {
                        pageTitle: 'រៀបចំកម្មវិធីចំណាយត្រីមាស',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'តាមដានអនុវត្តថវិកា', url: '/' },
                            { title: 'រៀបចំកម្មវិធីចំណាយត្រីមាស', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-monitoring/income-implementing-result',
                    name: 'IncomeExpenseResult',
                    component: () => import('@/views/pages/modules/budget-monitoring/income-implementing-result/_income_implementing_result.vue'),
                    meta: {
                        pageTitle: 'ចុះលទ្ធផលអនុវត្តចំណូល',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'តាមដានអនុវត្តថវិកា', url: '/' },
                            { title: 'ចុះលទ្ធផលអនុវត្តចំណូល', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {
                    path: 'module/budget-monitoring/expense-implementing-result',
                    name: 'IncomeExpenseResult',
                    component: () => import('@/views/pages/modules/budget-monitoring/income-implementing-result/_expenditure_implementing_result.vue'),
                    meta: {
                        pageTitle: 'ចុះលទ្ធផលអនុវត្តចំណាយ',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'តាមដានអនុវត្តថវិកា', url: '/' },
                            { title: 'ចុះលទ្ធផលអនុវត្តចំណាយ', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-monitoring/expenditure-certificate',
                    name: 'ExpenditureCertificate',
                    component: () => import('@/views/pages/modules/budget-monitoring/expenditure-certificate/Index.vue'),
                    meta: {
                        pageTitle: 'ចុះសលាកបត្រចំណាយ',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'តាមដានអនុវត្តថវិកា', url: '/' },
                            { title: 'ចុះសលាកបត្រចំណាយ', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-monitoring/credit-movement',
                    name: 'CreditMovement',
                    component: () => import('@/views/pages/modules/budget-monitoring/credit-movement/Index.vue'),
                    meta: {
                        pageTitle: 'ចលនាឥណទាន',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'តាមដានអនុវត្តថវិកា', url: '/' },
                            { title: 'ចលនាឥណទាន', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                // =============================================================================
                // Report
                // =============================================================================
                {           
                    path: 'report',
                    name: 'Report',
                    component: () => import('@/views/pages/reports/Index.vue'),
                    meta: {
                        pageTitle: 'របាយការណ៍',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/' },
                            { title: 'របាយការណ៍', active: true },
                        ],
                        rule: 'editor'
                    }
                },

                    {           
                        path: 'report/program',
                        name: 'ReportProgram',
                        component: () => import('@/views/pages/modules/program-management/_Program.vue'),//import('@/views/pages/reports/program/Index.vue'),
                        meta: {
                            pageTitle: 'របាយការណ៍កម្មវិធី',
                            breadcrumb: [
                                { title: 'ទំព័រដើម', url: '/' },
                                { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                                { title: 'របាយការណ៍កម្មវិធី', active: true },
                            ],
                            rule: 'editor'
                        }
                    },
                    {           
                        path: 'report/pip',
                        name: 'ReportPIP',
                        component: () => import('@/views/pages/modules/budget-arrangement/pip/Index.vue'), //import('@/views/pages/reports/pip/Index.vue'),
                        meta: {
                            pageTitle: 'របាយការណ៍គម្រោងវិនិយោគ',
                            breadcrumb: [
                                { title: 'ទំព័រដើម', url: '/' },
                                { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                                { title: 'របាយការណ៍គម្រោងវិនិយោគ', active: true },
                            ],
                            rule: 'editor'
                        }
                },
                    {           
                        path: 'report/bsp',
                        name: 'ReportBSP',
                        component: () => import('@/views/pages/modules/budget-arrangement/bsp/Index.vue'),//import('@/views/pages/reports/bsp/Index.vue'),
                        meta: {
                            pageTitle: 'របាយការណ៍ផែនការថវិកា',
                            breadcrumb: [
                                { title: 'ទំព័រដើម', url: '/' },
                                { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                                { title: 'របាយការណ៍ផែនការថវិកា', active: true },
                            ],
                            rule: 'editor'
                        }
                },
                    {           
                        path: 'report/pb',
                        name: 'ReportPB',
                        component: () => import('@/views/pages/modules/budget-arrangement/pb/Index.vue'),//import('@/views/pages/reports/pb/Index.vue'),
                        meta: {
                            pageTitle: 'របាយការណ៍គម្រោងថវិកាប្រចាំឆ្នាំ',
                            breadcrumb: [
                                { title: 'ទំព័រដើម', url: '/' },
                                { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                                { title: 'របាយការណ៍គម្រោងថវិកាប្រចាំឆ្នាំ', active: true },
                            ],
                            rule: 'editor'
                        }
                },
                    {           
                        path: 'report/quarterly',
                        name: 'ReportQuarterly',
                        component: () => import('@/views/pages/modules/budget-monitoring/income-expense-arrangement/Index.vue'),//import('@/views/pages/reports/quarterly/Index.vue'),
                        meta: {
                            pageTitle: 'របាយការណ៍ចំណូល/ចំណាយត្រីមាស',
                            breadcrumb: [
                                { title: 'ទំព័រដើម', url: '/' },
                                { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                                { title: 'របាយការណ៍ចំណូល/ចំណាយត្រីមាស', active: true },
                            ],
                            rule: 'editor'
                        }
                },
                // {           
                //     path: 'report/income',
                //     name: 'ReportIncome',
                //     component: () => import('@/views/pages/modules/budget-monitoring/income-implementing-result/Index.vue'),//import('@/views/pages/reports/income/Index.vue'),
                //     meta: {
                //         pageTitle: 'របាយការណ៍ចំណូល',
                //         breadcrumb: [
                //             { title: 'ទំព័រដើម', url: '/' },
                //             { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                //             { title: 'របាយការណ៍ចំណូល', active: true },
                //         ],
                //         rule: 'editor'
                //     }
                // }
                // , {           
                //         path: 'report/pip',
                //         name: 'ReportPIP',
                //         component: () => import('@/views/pages/reports/pip/Index.vue'),
                //         meta: {
                //             pageTitle: 'របាយការណ៍គម្រោងវិនិយោគ',
                //             breadcrumb: [
                //                 { title: 'ទំព័រដើម', url: '/' },
                //                 { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                //                 { title: 'របាយការណ៍គម្រោងវិនិយោគ', active: true },
                //             ],
                //             rule: 'editor'
                //         }
                //  },
                    ,{           
                        path: 'report/credit-movement',
                        name: 'ReportCreditMovement',
                        component: () => import('@/views/pages/modules/budget-monitoring/credit-movement/Index.vue'), //import('@/views/pages/reports/credit-movement/Index.vue'),
                        meta: {
                            pageTitle: 'របាយការណ៍ចលនាឥណទាន',
                            breadcrumb: [
                                { title: 'ទំព័រដើម', url: '/' },
                                { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report' },
                                { title: 'របាយការណ៍ចលនាឥណទាន', active: true },
                            ],
                            rule: 'editor'
                        }
                    },
                {
                    path: '/nofication',
                    name: 'nofication',
                    component: () => import('@/views/pages/notification/Index.vue'),
                    meta: {
                        // pageTitle: 'List View',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក', url: '/notification' },
                            { title: 'ការជូនដំណឹង', active: true },
                        ],
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Incentives
                // =============================================================================
                {
                    path: '/officer',
                    name: 'officer',
                    component: () => import('@/views/pages/officers/Index.vue'),
                    meta: {
                        // pageTitle: 'List View',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក', url: '/officer' },
                            { title: 'បញ្ជីសមាជិក', active: true },
                        ],
                        rule: 'editor'
                    },
                },
                {
                    path: '/officer/form',
                    name: 'officer-form',
                    component: () => import('@/views/pages/officers/tabs/_OfficerForm.vue'),
                    meta: {
                        // pageTitle: 'List View',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក', url: '/officer' },
                            { title: 'បង្កើតសមាជិក', active: true },
                        ],
                        rule: 'editor'
                    },
                },
                // {
                //     path: '/officer/form',
                //     name: 'data-officer-form',
                //     component: () => import('@/views/pages/officers/form/UserSettings.vue'),
                //     meta: {
                //         breadcrumb: [
                //             { title: 'ទំព័រដើម', url: '/' },
                //             { title: 'បញ្ជីសមាជិកបើកប្រាក់ឧបត្ថម្ភ', url: '/officer' },
                //             { title: 'ទម្រង់បង្កើត', active: true },
                //         ],
                //         pageTitle: 'រាយនាមមន្ត្រី',
                //         rule: 'editor'
                //     },
                // },
                // {
                //     path: '/officer/edit/:id',
                //     name: 'data-officer-edit',
                //     component: () => import('@/views/pages/officers/form/UserSettings.vue'),
                //     meta: {
                //         breadcrumb: [
                //             { title: 'ទំព័រដើម', url: '/' },
                //             { title: 'បញ្ជីសមាជិកបើកប្រាក់ឧបត្ថម្ភ', url: '/officer' },
                //             { title: 'ពត៌មានលម្អិត', active: true },
                //         ],
                //         pageTitle: 'រាយនាមមន្ត្រី',
                //         rule: 'editor'
                //     },
                // },
                {
                    path: '/officer/activity/:id',
                    name: 'data-officer-activity',
                    component: () => import('@/views/pages/officers/tabs/OfficerDetail.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីសមាជិក', url: '/officer' },
                            { title: 'ពត៌មានលម្អិត', active: true },
                        ],
                        pageTitle: 'រាយនាមមន្ត្រី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive/composition',
                    name: 'data-composition-form-list',
                    component: () => import('@/views/pages/compositions/data-list/list-view/DataListListView.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រាយនាមមន្ត្រី'},
                            { title: 'រាយនាមមន្ត្រីផ្លាស់ប្តូរសមាសភាព', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងប្រាក់ឧបត្ថម្ភ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive/composition-request',
                    name: 'data-composition-request-list',
                    component: () => import('@/views/pages/compositions-request/data-list/list-view/DataListListView.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាង'},
                            { title: 'ការផ្លាស់ប្តូរសមាសភាព', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងប្រាក់ឧបត្ថម្ភ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive/composition-request/form',
                    name: 'data-composition-request-form',
                    component: () => import('@/views/pages/compositions-request/form/FormWizard.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ទម្រង់', url: '/incentive/composition-request'},
                            { title: 'ការផ្លាស់ប្តូរសមាសភាព', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងប្រាក់ឧបត្ថម្ភ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive/composition-request/edit/:id',
                    name: 'data-composition-request-edit',
                    component: () => import('@/views/pages/compositions-request/form/FormWizard.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ទម្រង់', url: '/incentive/composition-request'},
                            { title: 'ការផ្លាស់ប្តូរសមាសភាព', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងប្រាក់ឧបត្ថម្ភ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive',
                    name: 'data-incentive-list',
                    // component: () => import('@/views/pages/incentives/data-list/list-view/DataListListView.vue'),
                    component: () => import('@/views/pages/officers/tabs/_TabIncentive.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក'},
                            { title: 'តារាងបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', active: true },
                        ],
                        // pageTitle: 'List View',
                        rule: 'editor'
                    },
                },
                {
                    path: '/request',
                    name: 'data-request',
                    // component: () => import('@/views/pages/incentives/data-list/list-view/DataListListView.vue'),
                    component: () => import('@/views/pages/officers/tabs/_TabRequest.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក'},
                            { title: 'សំណើរ', active: true },
                        ],
                        // pageTitle: 'List View',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive/form',
                    name: 'data-incentive-form',
                    component: () => import('@/views/pages/incentives/incentive-form-wizard/FormWizard.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បង្កើតប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'ការគ្រប់គ្រងប្រាក់ឧបត្ថម្ភ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/incentive/edit/:id',
                    name: 'data-incentive-edit',
                    component: () => import('@/views/pages/incentives/incentive-form-wizard/FormWizard.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បង្កើតប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'ការគ្រប់គ្រងប្រាក់ឧបត្ថម្ភ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/payment-method',
                    name: 'account-paymentMethod-list',
                    component: () => import('@/views/pages/account/payment-method/PaymentMethodList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/payment-method/form',
                    name: 'account-paymentMethod-form',
                    component: () => import('@/views/pages/account/payment-method/PaymentMethodForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/category',
                    name: 'account-category-list',
                    component: () => import('@/views/pages/account/category/CategoryList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/category/form',
                    name: 'account-category-form',
                    component: () => import('@/views/pages/account/category/CategoryForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/account',
                    name: 'account-account-list',
                    component: () => import('@/views/pages/account/account/AccountList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/account/form',
                    name: 'account-account-form',
                    component: () => import('@/views/pages/account/account/AccountForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/account-group',
                    name: 'account-account-group-list',
                    component: () => import('@/views/pages/account/account-group/AccountGroupList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ក្រុមគណនីយ'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/account-group/form',
                    name: 'account-account-group-form',
                    component: () => import('@/views/pages/account/account-group/AccountGroupForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ក្រុមគណនីយ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/account-type',
                    name: 'account-account-type-list',
                    component: () => import('@/views/pages/account/account-type/AccountTypeList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ប្រភេទគណនី'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/account-type/form',
                    name: 'account-account-type-form',
                    component: () => import('@/views/pages/account/account-type/AccountTypeForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ប្រភេទគណនី'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/bank-account',
                    name: 'account-bank-account-list',
                    component: () => import('@/views/pages/account/bank-account/BankAccountList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គណនីធនាគារ'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/bank-account/form',
                    name: 'account-bank-account-form',
                    component: () => import('@/views/pages/account/bank-account/BankAccountForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គណនីធនាគារ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/expense',
                    name: 'account-expense-list',
                    component: () => import('@/views/pages/account/expense/Index.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការចំណាយ', url: '/account/expense'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/expense/form',
                    name: 'account-expense-form',
                    component: () => import('@/views/pages/account/expense/ExpenseForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការចំណាយ', url: '/account/expense'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/expense/edit/:id',
                    name: 'account-expense-edit',
                    component: () => import('@/views/pages/account/expense/ExpenseForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការចំណាយ', url: '/account/expense'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                
                {
                    path: '/account/chart-account',
                    name: 'account-chart-account',
                    component: () => import('@/views/pages/account/coa/Index.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងគណនី', url: '/account/chart-account'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/chart-account/form',
                    name: 'account-chart-account-form',
                    component: () => import('@/views/pages/account/coa/COAForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងគណនី', url: '/account/chart-account'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },

                {
                    path: '/account/chart-account/edit/:id',
                    name: 'account-chart-account-edit',
                    component: () => import('@/views/pages/account/coa/COAForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងគណនី', url: '/account/chart-account'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },


                {
                    path: '/account/income',
                    name: 'account-income-list',
                    component: () => import('@/views/pages/account/income/Index.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការគ្រប់គ្រងចំណូល', url: '/account/income'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/income/form',
                    name: 'account-income-form',
                    component: () => import('@/views/pages/account/income/IncomeForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការគ្រប់គ្រងចំណូល', url: '/account/income'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/income/edit/:id',
                    name: 'account-income-edit',
                    component: () => import('@/views/pages/account/income/IncomeForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការគ្រប់គ្រងចំណូល', url: '/account/income'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/taxable-salary',
                    name: 'account-taxable-salary-list',
                    component: () => import('@/views/pages/account/taxable-salary/TaxList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការគណនាពន្ធ', url: '/account/taxable-salary'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/taxable-salary/form',
                    name: 'account-taxable-salary-form',
                    component: () => import('@/views/pages/account/taxable-salary/TaxForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការគណនាពន្ធ', url: '/account/taxable-salary'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/taxable-salary/edit/:id',
                    name: 'account-taxable-salary-edit',
                    component: () => import('@/views/pages/account/taxable-salary/TaxForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ការគណនាពន្ធ', url: '/account/taxable-salary'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/officer-payment',
                    name: 'account-officer-payment',
                    component: () => import('@/views/pages/officers/tabs/_TabPayable.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងការបង់ប្រាក់'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'ការបង់ប្រាក់',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/officer-payable-form',
                    name: 'account-officer-payable-form',
                    component: () => import('@/views/pages/officers/tabs/_PayableForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងការបង់ប្រាក់'},
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'ការបង់ប្រាក់',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Incentives Setup
                // =============================================================================
                {
                    path: '/setup/department',
                    name: 'department-list',
                    component: () => import('@/views/pages/setup/department/DepartmentList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងផ្នែក'},
                            { title: 'List View', active: true },
                        ],
                        pageTitle: 'រៀបចំទិន្នន័យ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/department/form',
                    name: 'department-form',
                    component: () => import('@/views/pages/setup/department/DepartmentForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រៀបចំទិន្នន័យ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់បំពេញ',
                        rule: 'editor'
                    },
                },
                
                {
                    path: '/setup/circular',
                    name: 'circular-list',
                    component: () => import('@/views/pages/setup/circular/CircularList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងផ្នែក'},
                            { title: 'List View', active: true },
                        ],
                        pageTitle: 'រៀបចំទិន្នន័យ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/circular/form',
                    name: 'circular-form',
                    component: () => import('@/views/pages/setup/circular/CircularForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រៀបចំទិន្នន័យ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់បំពេញ',
                        rule: 'editor'
                    },
                },
                
                {
                    path: '/setup/entity',
                    name: 'entity-list',
                    component: () => import('@/views/pages/setup/entity/EntityList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងផ្នែក'},
                            { title: 'List View', active: true },
                        ],
                        pageTitle: 'រៀបចំទិន្នន័យ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/entity/form',
                    name: 'entity-form',
                    component: () => import('@/views/pages/setup/entity/EntityForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រៀបចំទិន្នន័យ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់បំពេញ',
                        rule: 'editor'
                    },
                },
                
                {
                    path: '/setup/role',
                    name: 'role-list',
                    component: () => import('@/views/pages/setup/role/RoleList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងផ្នែក'},
                            { title: 'List View', active: true },
                        ],
                        pageTitle: 'រៀបចំទិន្នន័យ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/role/form',
                    name: 'role-form',
                    component: () => import('@/views/pages/setup/role/RoleForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រៀបចំទិន្នន័យ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់បំពេញ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/sector',
                    name: 'sector-list',
                    component: () => import('@/views/pages/setup/sector/SectorList.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'តារាងផ្នែក'},
                            { title: 'List View', active: true },
                        ],
                        pageTitle: 'រៀបចំទិន្នន័យ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/sector/form',
                    name: 'sector-form',
                    component: () => import('@/views/pages/setup/sector/SectorForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រៀបចំទិន្នន័យ'},
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់បំពេញ',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // Incentives Report
                // =============================================================================
                {
                    path: '/report/incentive',
                    name: 'report-incentive',
                    component: () => import('@/views/pages/reports/IncentiveReport.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីរបាយការណ៍'},
                            { title: 'របាយការណ៍ការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/letter_to_bank/:id',
                    name: 'report-incentive-letter-to-bank',
                    component: () => import('@/views/pages/reports/IncentiveLetterToBank.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/letter_to_GDNT/:id',
                    name: 'report-incentive-lettter-to-GDNT',
                    component: () => import('@/views/pages/reports/IncentiveLetterToGDNT.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/annex_to_GDNT/:id',
                    name: 'report-incentive-incentive-to-GDNT',
                    component: () => import('@/views/pages/reports/IncentiveAnnexToGDNT.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/incentive_to_bank/:id',
                    name: 'report-incentive-incentive-to-bank',
                    component: () => import('@/views/pages/reports/IncentiveToBank.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/incentive_to_lm/:id',
                    name: 'report-incentive-incentive-to-lm',
                    component: () => import('@/views/pages/reports/IncentiveLetterToLM.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/incentive_annex_to_lm/:id',
                    name: 'report-incentive-annex-to-lm',
                    component: () => import('@/views/pages/reports/IncentiveAnnexToLM.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/incentive/annex/:id',
                    name: 'report-incentive-annex',
                    component: () => import('@/views/pages/reports/IncentiveAnnex.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'របាយការណ៍លម្អិតការបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive/list'},
                            { title: 'លម្អិត', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/entity',
                    name: 'report-entity',
                    component: () => import('@/views/pages/reports/IncentiveByEntity.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីរបាយការណ៍'},
                            { title: 'របាយការណ៍ប្រាក់ឧបត្ថម្ភតាមវិស័យ', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/bank',
                    name: 'report-bank',
                    component: () => import('@/views/pages/reports/IncentiveByBank.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីរបាយការណ៍'},
                            { title: 'របាយការណ៍ប្រាក់ឧបត្ថម្ភតាមធនាគារ', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/quarter',
                    name: 'report-quarter',
                    component: () => import('@/views/pages/reports/IncentiveByQuarter.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីរបាយការណ៍'},
                            { title: 'របាយការណ៍ប្រាក់ឧបត្ថម្ភតាមត្រីមាស', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/monthly',
                    name: 'report-monthly',
                    component: () => import('@/views/pages/reports/IncentiveMonthly.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីរបាយការណ៍'},
                            { title: 'របាយការណ៍ប្រាក់ឧបត្ថម្ភប្រចាំខែ', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                {
                    path: '/report/summary_income_outcome',
                    name: 'report-summary-income-outcome',
                    component: () => import('@/views/pages/reports/SummaryIncomeOutcome.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'បញ្ជីរបាយការណ៍'},
                            { title: 'របាយការណ៍សង្ខេបចំណូល ចំណាយ', active: true },
                        ],
                        pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },
                // Generic PDF reports (must come after the fixed /report/* pages above)
                {
                    path: '/report/:report_type',
                    name: 'report',
                    component: () => import('@/views/pages/reports/ReportViewer.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងរបាយការណ៍', url: '/report'},
                            { title: 'របាយការណ៍', active: true },
                        ],
                        // pageTitle: 'របាយការណ៍',
                        rule: 'editor'
                    },
                },

                // =============================================================================
                // Settings
                // =============================================================================
                {
                    path: '/setting',
                    name: 'setting',
                    component: () => import('@/views/pages/settings/Setting.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ទំព័រដើម'},
                            { title: 'ការកំណត់', active: true },
                        ],
                        pageTitle: 'ការកំណត់',
                        rule: 'editor'
                    },
                },
                // =============================================================================
                // File Manager
                // =============================================================================
                {
                    path: '/file-manager',
                    name: 'file-manager',
                    component: () => import('./views/file-managers/Index.vue'),
                    meta: {
                        pageTitle: 'ឃាំ្លងឯកសារ',
                        breadcrumb: [
                            { title: 'Dashboard', url: '/' },
                            { title: 'ឃាំ្លងឯកសារ', url: '/' },
                            { title: 'ឃាំ្លងឯកសារ', active: true },
                        ],
                        rule: 'editor',
                    }
                },

            ],
        },
        
         // =============================================================================
        // FULL PAGE LAYOUTS
        // =============================================================================
        {
            path: '',
            component: () => import('@/layouts/full-page/FullPage.vue'),
            children: [
        // =============================================================================
        // PAGES
        // =============================================================================
                {
                    path: '/callback',
                    name: 'auth-callback',
                    component: () => import('@/views/Callback.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/login',
                    name: 'page-login',
                    component: () => import('@/views/pages/login/Login.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/register',
                    name: 'page-register',
                    component: () => import('@/views/pages/register/Register.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/forgot-password',
                    name: 'page-forgot-password',
                    component: () => import('@/views/pages/ForgotPassword.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/reset-password',
                    name: 'page-reset-password',
                    component: () => import('@/views/pages/ResetPassword.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/lock-screen',
                    name: 'page-lock-screen',
                    component: () => import('@/views/pages/LockScreen.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/comingsoon',
                    name: 'page-coming-soon',
                    component: () => import('@/views/pages/ComingSoon.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-404',
                    name: 'page-error-404',
                    component: () => import('@/views/pages/Error404.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-500',
                    name: 'page-error-500',
                    component: () => import('@/views/pages/Error500.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/not-authorized',
                    name: 'page-not-authorized',
                    component: () => import('@/views/pages/NotAuthorized.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/maintenance',
                    name: 'page-maintenance',
                    component: () => import('@/views/pages/Maintenance.vue'),
                    meta: {
                        rule: 'editor'
                    }
                },
            ]
        },
        
        // Redirect to 404 page, if no match found
        {
            path: '*',
            redirect: '/pages/error-404'
        }
    ],
})

router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
    if (appLoading) {
        appLoading.style.display = "none";
    }
})

router.beforeEach((to, from, next) => {
    firebase.auth().onAuthStateChanged(() => {

        // get firebase current user
        const firebaseCurrentUser = firebase.auth().currentUser

        // if (
        //     to.path === "/pages/login" ||
        //     to.path === "/pages/forgot-password" ||
        //     to.path === "/pages/error-404" ||
        //     to.path === "/pages/error-500" ||
        //     to.path === "/pages/register" ||
        //     to.path === "/callback" ||
        //     to.path === "/pages/comingsoon" ||
        //     (auth.isAuthenticated() || firebaseCurrentUser)
        // ) {
        //     return next();
        // }

        // If auth required, check login. If login fails redirect to login page
        if(to.meta.authRequired) {
          if (!(auth.isAuthenticated() || firebaseCurrentUser)) {
            router.push({ path: '/pages/login', query: { to: to.path } })
          }
        }

        return next()
        // Specify the current path as the customState parameter, meaning it
        // will be returned to the application after auth
        // auth.login({ target: to.path });

    });

});

export default router
