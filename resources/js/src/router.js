import { createRouter, createWebHistory } from 'vue-router'
import { can } from '@/acl/access.js'
import { homePath } from '@/layouts/components/vertical-nav-menu/menuPermissions.js'

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
            // Home is the first page of the user's own menu (Administration › Menus), else the dashboard.
            redirect: () => homePath(),
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
                // =============================================================================
                // User
                // =============================================================================
                {
                    path: '/user',
                    name: 'data-user-list',
                    component: () => import('@/views/pages/settings/_User.vue'),
                    meta: {
                        permission: ['list_users'],
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
                        permission: ['edit_users'], allowSelf: 'userId',
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
                        permission: ['audit'],
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
                        permission: ['list_roles'],
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
                    path: '/menu',
                    name: 'app-menu',
                    component: () => import('@/views/pages/settings/_Menu.vue'),
                    meta: {
                        permission: ['list_menu'],
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'អ្នកប្រើប្រាស់' },
                            { title: 'ម៉ឺនុយ', active: true },
                        ],
                        pageTitle: 'ម៉ឺនុយ',
                        rule: 'editor'
                    },
                },
                {
                    path: '/role/form/:roleId',
                    name: 'data-role-form',
                    component: () => import('@/views/pages/roles/RoleForm.vue'),
                    meta: {
                        permission: ['role_permissions'],
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
                        permission: ['reports'],
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
                    path: '/nofication',
                    name: 'nofication',
                    component: () => import('@/views/pages/notification/Index.vue'),
                    meta: {
                        permission: ['list_notifications'],
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
                        permission: ['list_officer'],
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
                        permission: ['create_officer', 'edit_officer'],
                        pageTitle: 'ចុះឈ្មោះសមាជិក',
                        breadcrumb: [
                            { title: 'ទំព័រដើម', url: '/' },
                            { title: 'រាយនាមសមាជិក', url: '/officer' },
                            { title: 'ចុះឈ្មោះសមាជិក', active: true },
                        ],
                        rule: 'editor'
                    },
                },
                {
                    path: '/officer/activity/:id',
                    name: 'data-officer-activity',
                    component: () => import('@/views/pages/officers/tabs/OfficerDetail.vue'),
                    meta: {
                        permission: ['show_officer'],
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
                    component: () => import('@/views/pages/officers/tabs/_TabIncentive.vue'),
                    meta: {
                        permission: ['list_officer_incentive'],
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
                    component: () => import('@/views/pages/officers/tabs/_TabRequest.vue'),
                    meta: {
                        permission: ['list_officer_request'],
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
                        permission: ['list_expense'],
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
                        permission: ['list_account'],
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
                        permission: ['list_income'],
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
                        permission: ['list_officer_payable'],
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
                        permission: ['create_officer_payable', 'edit_officer_payable'],
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
                        permission: ['reports'],
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
                    // Nothing in Settings for this role: back to the home page (the API refuses it as well)
                    beforeEnter: async () => {
                        const { canOpenSettings } = await import('@/views/pages/settings/settingsAccess.js')
                        return canOpenSettings() ? true : { path: '/' }
                    },
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
                        permission: ['file_manager_list'],
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
                    component: () => import('@/views/pages/ErrorPage.vue'),
                    meta: {
                        errorCode: 404,
                        title: 'រកមិនឃើញទំព័រ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/error-500',
                    name: 'page-error-500',
                    component: () => import('@/views/pages/ErrorPage.vue'),
                    meta: {
                        errorCode: 500,
                        title: 'កំហុសម៉ាស៊ីនមេ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                {
                    path: '/pages/not-authorized',
                    name: 'page-not-authorized',
                    component: () => import('@/views/pages/ErrorPage.vue'),
                    meta: {
                        errorCode: 403,
                        title: 'គ្មានសិទ្ធិចូលប្រើ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
                // Any other address: the 404 page in place, so the address that failed stays in the bar
                {
                    path: '/:pathMatch(.*)*',
                    name: 'page-not-found',
                    component: () => import('@/views/pages/ErrorPage.vue'),
                    meta: {
                        errorCode: 404,
                        title: 'រកមិនឃើញទំព័រ', // browser tab only (no page header)
                        rule: 'editor'
                    }
                },
            ]
        },
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

// The signed-in user's id (from login), for pages a user may always open about themselves
function currentUserId() {
    try {
        return String((JSON.parse(localStorage.getItem('userInfo')) || {}).id || '')
    } catch (e) {
        return ''
    }
}

router.beforeEach((to, from, next) => {
    // Pages marked authRequired need a signed-in user (the API checks every request as well)
    const signedIn = localStorage.getItem('accessToken') && localStorage.getItem('userInfo') // as auth.isUserLoggedIn
    if (to.meta.authRequired && !signedIn) {
        return next({ path: '/pages/login', query: { to: to.path } })
    }
    // meta.permission: the role needs at least one of them (permission names or feature keys, see acl/access.js).
    // meta.allowSelf: the route param holding a user id; that user may open the page about themselves.
    const needed = to.matched.flatMap(record => record.meta.permission || [])
    if (needed.length && !needed.some(permission => can(permission))) {
        const self = to.meta.allowSelf && String(to.params[to.meta.allowSelf]) === currentUserId()
        if (!self) return next({ name: 'page-not-authorized' })
    }
    return next()
})

export default router
