export default {
    adminRoutes: [
        {
            path: '/admin',
            label: 'Home',
            icon: 'home',
            subPaths: [],
        },
        {
            path: '/admin/emailtemplates',
            label: 'Email Templates',
            icon: 'email',
            subPaths: ['/admin/emailtemplate/'],
        },
        {
            path: '/admin/endprelaunch',
            label: 'End Prelaunch',
            icon: 'stop',
            subPaths: [],
        },
        {
            path: '/admin/changecontent',
            label: 'Change Content',
            icon: 'edit',
            subPaths: [],
        },
        {
            path: '/admin/users',
            label: 'User Search',
            icon: 'search',
            subPaths: ['/admin/user/'],
        },
        {
            path: '/admin/gallery',
            label: 'Gallery',
            icon: 'collections',
            subPaths: [],
        },
        {
            path: '/admin/logs',
            label: 'System logs',
            icon: 'library_books',
            subPaths: [],
        },
    ],
};
