import Dashboard from './Pages/Dashboard/Dashboard.vue'
import Profile from './Pages/Profile/Profile.vue'
import Team from './Pages/Team/Team.vue'
import Billing from './Pages/Billing/Billing.vue'
import Admin from './Pages/Admin/Admin.vue'

export default
[
    {
        path: '/admin', 
        name: 'admin',
        component: Admin,
    },
    { 
        path: '/', 
        name: 'dashboard',
        component: Dashboard,
    },
    { 
        path: '/profile', 
        name: 'profile',
        component: Profile,
    },
    { 
        path: '/team', 
        name: 'team',
        component: Team,
    },
    { 
        path: '/billing', 
        name: 'billing',
        component: Billing,
    },
]
