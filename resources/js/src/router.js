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
                            title: 'ផ្ទាំងគ្រប់គ្រង', // browser tab only (no page header)
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
                        pageTitle: 'រាយនាមអ្នកប្រើប្រាស់',
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
                            { title: 'អ្នកប្រើប្រាស់', url: '/user' },
                            { title: 'ព័ត៌មានអ្នកប្រើប្រាស់', active: true },
                        ],
                        pageTitle: 'ព័ត៌មានអ្នកប្រើប្រាស់',
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
                            { title: 'អ្នកប្រើប្រាស់', url: '/user' },
                            { title: 'កែប្រែអ្នកប្រើប្រាស់', active: true },
                        ],
                        pageTitle: 'កែប្រែអ្នកប្រើប្រាស់',
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
                        pageTitle: 'កំណត់ហេតុប្រើប្រាស់',
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
                            { title: 'បញ្ជីសំណើរ', active: true },
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
                            { title: 'បញ្ជីសំណើរ', url: '/policy/list' },
                            { title: 'ទម្រង់សំណើរ', active: true },
                        ],
                        pageTitle: 'ទម្រង់សំណើរ',
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
                            { title: 'សិទ្ធិ' },
                            { title: 'គ្រប់គ្រងសិទ្ធិ', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងសិទ្ធិ',
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
                            { title: 'គ្រប់គ្រងសិទ្ធិ', url: '/role' },
                            { title: 'ទម្រង់សិទ្ធិ', active: true },
                        ],
                        pageTitle: 'ទម្រង់សិទ្ធិ',
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
                            { title: 'គ្រប់គ្រងសិទ្ធិ', url: '/role' },
                            { title: 'សិទ្ធិម៉ឺនុយ', active: true },
                        ],
                        pageTitle: 'សិទ្ធិម៉ឺនុយ',
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
                            { title: 'គ្រប់គ្រងកម្មវិធី', url: '/module/program-arrangement' },
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
                            { title: 'គ្រប់គ្រងកម្មវិធី', url: '/module/program-arrangement' },
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
                    component: () => import('./views/pages/modules/program-management/_Subprogram.vue'),
                    meta: {
                        pageTitle: 'អនុកម្មវិធី',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'គ្រប់គ្រងកម្មវិធី', url: '/module/program-arrangement' },
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
                            { title: 'គ្រប់គ្រងកម្មវិធី', url: '/module/program-arrangement' },
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
                            { title: 'គ្រប់គ្រងកម្មវិធី', url: '/module/program-arrangement' },
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
                            { title: 'គ្រប់គ្រងកម្មវិធី', url: '/module/program-arrangement' },
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
                        pageTitle: 'បញ្ជីពិដានថវិកា',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'ពិដានថវិកា', url: '/module/budget-arrangement/budget-ceiling' },
                            { title: 'បញ្ជីពិដានថវិកា', active: true },
                        ],
                        rule: 'editor'
                    }
                },
                {           
                    path: 'module/budget-arrangement/pip',
                    name: 'PIP',
                    component: () => import('@/views/pages/modules/budget-arrangement/pip/index.vue'),
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
                    component: () => import('@/views/pages/modules/budget-arrangement/bsp/index.vue'),
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
                    component: () => import('@/views/pages/modules/budget-arrangement/pb/index.vue'),
                    meta: {
                        pageTitle: 'រៀបចំគម្រោងថវិកាប្រចាំឆ្នាំ',
                        breadcrumb: [
                            { title: 'ផ្ទាំងគ្រប់គ្រង', url: '/' },
                            { title: 'រៀបចំថវិកា', url: '/' },
                            { title: 'រៀបចំគម្រោងថវិកាប្រចាំឆ្នាំ', active: true },
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
                        component: () => import('@/views/pages/modules/budget-arrangement/pip/index.vue'), //import('@/views/pages/reports/pip/Index.vue'),
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
                        component: () => import('@/views/pages/modules/budget-arrangement/bsp/index.vue'),//import('@/views/pages/reports/bsp/Index.vue'),
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
                        component: () => import('@/views/pages/modules/budget-arrangement/pb/index.vue'),//import('@/views/pages/reports/pb/Index.vue'),
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
                        pageTitle: 'បញ្ជីការជូនដំណឹង',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក' },
                            { title: 'បញ្ជីការជូនដំណឹង', active: true },
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
                        pageTitle: 'រាយនាមសមាជិក',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គ្រប់គ្រងសមាជិក' },
                            { title: 'រាយនាមសមាជិក', active: true },
                        ],
                        rule: 'editor'
                    },
                },
                {
                    path: '/officer/form',
                    name: 'officer-form',
                    component: () => import('@/views/pages/officers/tabs/_OfficerForm.vue'),
                    meta: {
                        pageTitle: 'ចុះឈ្មោះសមាជិក',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រាយនាមសមាជិក', url: '/officer' },
                            { title: 'ចុះឈ្មោះសមាជិក', active: true },
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
                            { title: 'រាយនាមសមាជិក', url: '/officer' },
                            { title: 'ព័ត៌មានលម្អិតសមាជិក', active: true },
                        ],
                        pageTitle: 'ព័ត៌មានលម្អិតសមាជិក',
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
                            { title: 'គ្រប់គ្រងប្រាក់ឧបត្ថម្ភ' },
                            { title: 'រាយនាមមន្ត្រីផ្លាស់ប្តូរសមាសភាព', active: true },
                        ],
                        pageTitle: 'រាយនាមមន្ត្រីផ្លាស់ប្តូរសមាសភាព',
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
                            { title: 'គ្រប់គ្រងប្រាក់ឧបត្ថម្ភ' },
                            { title: 'សំណើរផ្លាស់ប្តូរសមាសភាព', active: true },
                        ],
                        pageTitle: 'សំណើរផ្លាស់ប្តូរសមាសភាព',
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
                            { title: 'សំណើរផ្លាស់ប្តូរសមាសភាព', url: '/incentive/composition-request' },
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'បង្កើតសំណើរផ្លាស់ប្តូរសមាសភាព',
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
                            { title: 'សំណើរផ្លាស់ប្តូរសមាសភាព', url: '/incentive/composition-request' },
                            { title: 'ទម្រង់កែប្រែ', active: true },
                        ],
                        pageTitle: 'កែប្រែសំណើរផ្លាស់ប្តូរសមាសភាព',
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
                            { title: 'ហិរញ្ញវត្ថុ' },
                            { title: 'បញ្ជីបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', active: true },
                        ],
                        pageTitle: 'បញ្ជីបើកផ្តល់ប្រាក់ឧបត្ថម្ភ',
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
                            { title: 'ហិរញ្ញវត្ថុ' },
                            { title: 'ទិន្នន័យសំណើរ', active: true },
                        ],
                        pageTitle: 'ទិន្នន័យសំណើរ',
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
                            { title: 'បញ្ជីបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive' },
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'បង្កើតប្រាក់ឧបត្ថម្ភ',
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
                            { title: 'បញ្ជីបើកផ្តល់ប្រាក់ឧបត្ថម្ភ', url: '/incentive' },
                            { title: 'ទម្រង់កែប្រែ', active: true },
                        ],
                        pageTitle: 'កែប្រែប្រាក់ឧបត្ថម្ភ',
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
                            { title: 'វិធីបង់ប្រាក់' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'វិធីបង់ប្រាក់',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/payment-method/form',
                    name: 'account-paymentMethod-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'វិធីបង់ប្រាក់', url: '/account/payment-method' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់វិធីបង់ប្រាក់',
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
                            { title: 'ប្រភេទផលិតផល/សេវាកម្ម' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'ប្រភេទផលិតផល/សេវាកម្ម',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/category/form',
                    name: 'account-category-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ប្រភេទផលិតផល/សេវាកម្ម', url: '/account/category' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់ប្រភេទផលិតផល/សេវាកម្ម',
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
                            { title: 'គណនី' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/account/form',
                    name: 'account-account-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គណនី', url: '/account/account' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់គណនី',
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
                            { title: 'ក្រុមគណនី' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'ក្រុមគណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/account-group/form',
                    name: 'account-account-group-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ក្រុមគណនី', url: '/account/account-group' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់ក្រុមគណនី',
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
                            { title: 'ប្រភេទគណនី' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'ប្រភេទគណនី',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/account-type/form',
                    name: 'account-account-type-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ប្រភេទគណនី', url: '/account/account-type' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់ប្រភេទគណនី',
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
                            { title: 'គណនីធនាគារ' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គណនីធនាគារ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/account/bank-account/form',
                    name: 'account-bank-account-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'គណនីធនាគារ', url: '/account/bank-account' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់គណនីធនាគារ',
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
                            { title: 'គ្រប់គ្រងចំណាយ', url: '/account/expense' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងចំណាយ',
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
                            { title: 'គ្រប់គ្រងចំណាយ', url: '/account/expense' },
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'បង្កើតចំណាយ',
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
                            { title: 'គ្រប់គ្រងចំណាយ', url: '/account/expense' },
                            { title: 'ទម្រង់កែប្រែ', active: true },
                        ],
                        pageTitle: 'កែប្រែចំណាយ',
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
                            { title: 'តារាងគណនី', url: '/account/chart-account' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'តារាងគណនី',
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
                            { title: 'តារាងគណនី', url: '/account/chart-account' },
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'បង្កើតគណនី',
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
                            { title: 'តារាងគណនី', url: '/account/chart-account' },
                            { title: 'ទម្រង់កែប្រែ', active: true },
                        ],
                        pageTitle: 'កែប្រែគណនី',
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
                            { title: 'គ្រប់គ្រងចំណូល', url: '/account/income' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'គ្រប់គ្រងចំណូល',
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
                            { title: 'គ្រប់គ្រងចំណូល', url: '/account/income' },
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'បង្កើតចំណូល',
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
                            { title: 'គ្រប់គ្រងចំណូល', url: '/account/income' },
                            { title: 'ទម្រង់កែប្រែ', active: true },
                        ],
                        pageTitle: 'កែប្រែចំណូល',
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
                            { title: 'ការគណនាពន្ធ', url: '/account/taxable-salary' },
                            { title: 'បញ្ជី', active: true },
                        ],
                        pageTitle: 'ការគណនាពន្ធ',
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
                            { title: 'ការគណនាពន្ធ', url: '/account/taxable-salary' },
                            { title: 'ទម្រង់បង្កើត', active: true },
                        ],
                        pageTitle: 'បង្កើតការគណនាពន្ធ',
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
                            { title: 'ការគណនាពន្ធ', url: '/account/taxable-salary' },
                            { title: 'ទម្រង់កែប្រែ', active: true },
                        ],
                        pageTitle: 'កែប្រែការគណនាពន្ធ',
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
                            { title: 'ហិរញ្ញវត្ថុ' },
                            { title: 'ទិន្នន័យបង់ប្រាក់', active: true },
                        ],
                        pageTitle: 'ទិន្នន័យបង់ប្រាក់',
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
                            { title: 'ហិរញ្ញវត្ថុ' },
                            { title: 'ទទួលប្រាក់', active: true },
                        ],
                        pageTitle: 'ទទួលប្រាក់',
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
                            { title: 'រៀបចំទិន្នន័យ' },
                            { title: 'ផ្នែក', active: true },
                        ],
                        pageTitle: 'ផ្នែក',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/department/form',
                    name: 'department-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ផ្នែក', url: '/setup/department' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់ផ្នែក',
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
                            { title: 'រៀបចំទិន្នន័យ' },
                            { title: 'សារាចរ', active: true },
                        ],
                        pageTitle: 'សារាចរ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/circular/form',
                    name: 'circular-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'សារាចរ', url: '/setup/circular' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់សារាចរ',
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
                            { title: 'រៀបចំទិន្នន័យ' },
                            { title: 'អង្គភាព', active: true },
                        ],
                        pageTitle: 'អង្គភាព',
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
                            { title: 'អង្គភាព', url: '/setup/entity' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់អង្គភាព',
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
                            { title: 'រៀបចំទិន្នន័យ' },
                            { title: 'សិទ្ធិប្រើប្រាស់', active: true },
                        ],
                        pageTitle: 'សិទ្ធិប្រើប្រាស់',
                        rule: 'editor'
                    },
                },
                {
                    path: '/setup/role/form',
                    name: 'role-form',
                    component: () => import('@/views/form-builder/DPlaceholderForm.vue'),
                    meta: {
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'សិទ្ធិប្រើប្រាស់', url: '/setup/role' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់សិទ្ធិប្រើប្រាស់',
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
                            { title: 'រៀបចំទិន្នន័យ' },
                            { title: 'វិស័យ', active: true },
                        ],
                        pageTitle: 'វិស័យ',
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
                            { title: 'វិស័យ', url: '/setup/sector' },
                            { title: 'ទម្រង់', active: true },
                        ],
                        pageTitle: 'ទម្រង់វិស័យ',
                        rule: 'editor'
                    },
                },
                // Report viewer: /report/{type} (types come from GET /api/v1/report/types)
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
                        pageTitle: 'របាយការណ៍',
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
                        pageTitle: 'ឃ្លាំងឯកសារ',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'ឃ្លាំងឯកសារ', active: true },
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
                        title: 'ចូលប្រើប្រាស់ប្រព័ន្ធ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/register',
                    name: 'page-register',
                    component: () => import('@/views/pages/register/Register.vue'),
                    meta: {
                        title: 'ចុះឈ្មោះ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/forgot-password',
                    name: 'page-forgot-password',
                    component: () => import('@/views/pages/ForgotPassword.vue'),
                    meta: {
                        title: 'ភ្លេចពាក្យសម្ងាត់', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/reset-password',
                    name: 'page-reset-password',
                    component: () => import('@/views/pages/ResetPassword.vue'),
                    meta: {
                        title: 'កំណត់ពាក្យសម្ងាត់ឡើងវិញ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/lock-screen',
                    name: 'page-lock-screen',
                    component: () => import('@/views/pages/LockScreen.vue'),
                    meta: {
                        title: 'ចាក់សោអេក្រង់', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/comingsoon',
                    name: 'page-coming-soon',
                    component: () => import('@/views/pages/ComingSoon.vue'),
                    meta: {
                        title: 'មកដល់ឆាប់ៗ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-404',
                    name: 'page-error-404',
                    component: () => import('@/views/pages/Error404.vue'),
                    meta: {
                        title: 'រកមិនឃើញទំព័រ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-500',
                    name: 'page-error-500',
                    component: () => import('@/views/pages/Error500.vue'),
                    meta: {
                        title: 'កំហុសម៉ាស៊ីនមេ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/not-authorized',
                    name: 'page-not-authorized',
                    component: () => import('@/views/pages/NotAuthorized.vue'),
                    meta: {
                        title: 'គ្មានសិទ្ធិចូលប្រើ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/maintenance',
                    name: 'page-maintenance',
                    component: () => import('@/views/pages/Maintenance.vue'),
                    meta: {
                        title: 'កំពុងថែទាំប្រព័ន្ធ', // browser tab only (no page header)
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
        // Fade out, then take it out of the page
        appLoading.classList.add("is-hidden");
        setTimeout(() => appLoading.remove(), 400);
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
