import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    scrollBehavior () {
        return { left: 0, top: 0 }
    },
    routes: [
        {
            // =============================================================================
            // MAIN LAYOUT ROUTES
            // =============================================================================
            path: '',
            component: () => import('./layouts/main/Main.vue'),
            // '/' matches this layout route itself (not a child), so the home redirect lives here.
            // As a child '/' route it never matched and the layout showed an empty page.
            redirect: '/dashboard',
            meta: { authRequired: true },
            children: [
                // Components

            // =============================================================================
            // Theme Routes
            // =============================================================================
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
                // =============================================================================
                // Program Management
                // =============================================================================
                // =============================================================================
                // Budget Arrangement
                // =============================================================================
                // =============================================================================
                // Budget Implementing Monitoring
                // =============================================================================
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
                // Records are added and edited in the side panel of the list
                { path: '/account/payment-method/form', redirect: '/account/payment-method' },

                // Records are added and edited in the side panel of the list
                { path: '/account/category/form', redirect: '/account/category' },

                // Records are added and edited in the side panel of the list
                { path: '/account/account/form', redirect: '/account/account' },

                // Records are added and edited in the side panel of the list
                { path: '/account/account-group/form', redirect: '/account/account-group' },

                // Records are added and edited in the side panel of the list
                { path: '/account/account-type/form', redirect: '/account/account-type' },

                // Records are added and edited in the side panel of the list
                { path: '/account/bank-account/form', redirect: '/account/bank-account' },
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
                // Records are added and edited in the side panel of the list
                { path: '/setup/department/form', redirect: '/setup/department' },
                
                // Records are added and edited in the side panel of the list
                { path: '/setup/circular/form', redirect: '/setup/circular' },
                
                // Records are added and edited in the side panel of the list
                { path: '/setup/entity/form', redirect: '/setup/entity' },
                
                // Records are added and edited in the side panel of the list
                { path: '/setup/role/form', redirect: '/setup/role' },
                // Records are added and edited in the side panel of the list
                { path: '/setup/sector/form', redirect: '/setup/sector' },
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
                        hideHeader: true, // the page has its own header
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
                    path: '/pages/login',
                    name: 'page-login',
                    component: () => import('@/views/pages/login/Login.vue'),
                    meta: {
                        title: 'ចូលប្រើប្រាស់ប្រព័ន្ធ', // browser tab only (no page header)
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
            ]
        },
        
        // Redirect to 404 page, if no match found
        {
            path: '/:pathMatch(.*)*',
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
    // Pages marked authRequired need a signed-in user (the API checks every request as well)
    const signedIn = localStorage.getItem('accessToken') && localStorage.getItem('userInfo') // as auth.isUserLoggedIn
    if (to.meta.authRequired && !signedIn) {
        return next({ path: '/pages/login', query: { to: to.path } })
    }
    return next()
})

export default router
