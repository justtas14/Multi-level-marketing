export default {
    adminRoutes: [
        {
            path: '/admin',
            label: 'Home',
            icon: "home"
        },
        {
            path: '/admin/emailtemplateslist',
            label: 'Email Templates',
            icon: 'email'
        },
        {
            path: '/admin/endprelaunch',
            label: 'End Prelaunch',
            icon: 'stop'
        },
        {
            path: '/admin/changecontent',
            label: 'Change Content',
            icon: 'edit'
        },
        {
            path: '/admin/users',
            label: 'User Search',
            icon: 'search',
            subRoute: ['user_search_details']
        },
        {
            path: '/admin/gallery',
            label: 'Gallery',
            icon: 'collections',
        },
        {
            path: '/admin/logs',
            label: 'System logs',
            icon: 'library_books',
        },
    ],
}