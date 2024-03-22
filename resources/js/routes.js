import Dashboard from './components/Dashboard.vue';
import ListAppointments from './pages/appointments/ListAppointments.vue';
import AppointmentForm from './pages/appointments/AppointmentForm.vue';
import UserList from './pages/users/UserList.vue';
import UpdateSetting from './pages/settings/UpdateSetting.vue';
import UpdateProfile from './pages/profile/UpdateProfile.vue';
import Login from './pages/auth/Login.vue';
// import Register from './pages/auth/Register.vue';
import ClientList from './pages/client/ClientList.vue';
import TechList from './pages/techrecomm/TechList.vue';
import MyPrio from './pages/myprio/MyPrio.vue';
import MyVsc from './pages/myvsc/MyVsc.vue';
import MyClosedPrio from './pages/mycloseprio/MyClosePrioList.vue';
import UserMenu from './pages/usermenu/UserMenuList.vue';
import Menu from './pages/menu/MenuList.vue';
import page404 from "./404.vue";
import TechRecomApprove from "./pages/techrecomm/TechRecommApprove.vue";
import Notifications from './pages/notification/index.vue';
import AssetMonitoring from './pages/assetmonitoring/AssetmList.vue';
import PalletMonitoring from './pages/palletmonitoring/PalletList.vue';
import UserSite from './pages/usersite/UserSite.vue';

export default [
    {
        path: '/login',
        name: 'admin.login',
        component: Login,
    },
    // {
    //     path: '/register',
    //     name: 'admin.register',
    //     component: Register,
    // },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: Dashboard,
    },

    {
        path: '/appointments',
        name: 'Appointments',
        component: ListAppointments,
    },

    {
        path: '/appointments/create',
        name: 'Appointments',
        component: AppointmentForm,
    },

    {
        path: '/appointments/:id/edit',
        name: 'Appointments',
        component: AppointmentForm,
    },

    {
        path: '/user',
        name: 'User',
        component: UserList,
    },

    {
        path: '/settings',
        name: 'Setting',
        component: UpdateSetting,
    },

    {
        path: '/profile',
        name: 'Profile',
        component: UpdateProfile,
    },

    {
        path: '/client',
        name: 'Client',
        component: ClientList,
    },

    {
        path: '/tech-recommendation',
        name: 'Tech Recomm',
        component: TechList,
    },
    {
        path: '/weekly-task-schedule/myprio',
        name: 'My Prio',
        component: MyPrio,
    },

    {
        path: '/weekly-task-schedule/my-closed-prio',
        name: 'My Closed Prio',
        component: MyClosedPrio,
    },
    {
        path: '/weekly-task-schedule/mycoa',
        name: 'My COA',
        component: MyVsc,
    },
    {
        path: '/usermenu',
        name: 'User Menu',
        component: UserMenu,
    },
    {
        path: '/menu',
        name: 'Menu',
        component: Menu,
    },
    // Add the following 404 route at the end
    {
        path: '/:catchAll(.*)',
        name: '404 Error Page',
        component: page404, // Replace with your actual 404 component
    },
    {
        path: '/notifications',
        name: 'Notifications',
        component: Notifications, // Replace with your actual 404 component
    },
    {
        path: '/tech-approved/:id/view',
        name: 'Tech-Approved',
        component: TechRecomApprove, // Replace with your actual 404 component
    },
    {
        path: '/asset-monitoring',
        name: 'Asset Monitoring',
        component: AssetMonitoring, // Replace with your actual 404 component
    },
  {
        path: '/pallet-monitoring',
        name: 'Pallet Monitoring',
        component: PalletMonitoring, // Replace with your actual 404 component
    },
  {
        path: '/usersite',
        name: 'User Site',
        component: UserSite, // Replace with your actual 404 component
    },
]
