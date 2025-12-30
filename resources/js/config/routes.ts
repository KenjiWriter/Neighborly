/**
 * Centralized route paths for frontend navigation.
 * Use these stable static paths instead of Wayfinder helpers to avoid runtime errors.
 */

export const routes = {
    // Public routes
    home: '/',
    login: '/login',
    register: '/register',

    // Auth flows
    forgotPassword: '/forgot-password',
    resetPassword: '/reset-password',
    verifyEmail: '/email/verify',
    confirmPassword: '/confirm-password',
    twoFactorChallenge: '/two-factor-challenge',

    // Main app
    dashboard: '/dashboard',

    // Account status
    accountStatus: '/account/status',
    accountPending: '/account/pending',
    accountRejected: '/account/rejected',

    // Community
    communityPrimary: '/communities/primary',

    // Maintenance
    maintenanceIndex: '/maintenance',
    maintenanceCreate: '/maintenance/create',
    maintenanceShow: (id: string | number) => `/maintenance/${id}`,
    maintenanceStore: '/maintenance',
    maintenanceAssign: (id: string | number) => `/maintenance/${id}/assign`,
    maintenanceStatus: (id: string | number) => `/maintenance/${id}/status`,

    // Finance
    financesOverview: '/finances/overview',

    // Documents
    documentsIndex: '/documents',
    documentsCreate: '/documents/upload',
    documentsStore: '/documents',
    documentsDownload: (id: string | number) => `/documents/${id}/download`,

    // Audit
    auditIndex: '/audit',

    // Announcements
    announcementsIndex: '/announcements',
    announcementsShow: (id: string | number) => `/announcements/${id}`,

    // Polls
    pollsIndex: '/polls',
    pollsShow: (id: string | number) => `/polls/${id}`,
    pollsVote: (id: string | number) => `/polls/${id}/vote`,

    // Settings
    settings: '/settings',
    settingsProfile: '/settings/profile',
    settingsPassword: '/settings/password',
    settingsAppearance: '/settings/appearance',
    settingsTwoFactor: '/settings/two-factor',

    // Auth actions (POST routes)
    logout: '/logout',

    // Admin
    adminUsersPending: '/admin/users/pending',
    adminUsersIndex: '/admin/users',
    adminUsersEdit: (userId: string | number) => `/admin/users/${userId}/edit`,
    adminUsersUpdate: (userId: string | number) => `/admin/users/${userId}`,
    adminUsersApprove: (userId: string | number) => `/admin/users/${userId}/approve`,
    adminUsersReject: (userId: string | number) => `/admin/users/${userId}/reject`,
} as const;

export type RouteKey = keyof typeof routes;
