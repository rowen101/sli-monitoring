
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
import TechRecommApproved from './pages/techrecomm/TechRecommApproved.vue';
import Notifications from './pages/notification/index.vue';
import AssetMonitoring from './pages/assetmonitoring/AssetmList.vue';
import AssetCategory from './pages/assetmonitoring/asset-category.vue';
import AssetItemMaster from './pages/assetmonitoring/asset-item-master.vue';
import PalletMonitoring from './pages/palletmonitoring/PalletList.vue';
import UserSite from './pages/usersite/UserSite.vue';
import joborder from './pages/joborder/JobOrderList.vue';
import JobOrderAction from './pages/joborder/JobOrderAction.vue';
import JobOrderRequestApproved from '@/pages/joborder/JobOrderRequestApproved.vue';

import MrfList from './pages/Mrf/MrfList.vue';
import MrfAction from './pages/Mrf/MrfAction.vue';
import MrfRequestApproved from '@/pages/Mrf/MrfRequestApproved.vue';
import PrintAsset from './pages/assetmonitoring/PrintAsset.vue';


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
        path: '/',
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
        component: Notifications,
    },
    {
        path: '/tech-approved/:id/view',
        name: 'Tech-Approved',
        component: TechRecommApproved,
    },
    {
        path: '/asset-category',
        name: 'Asset Category',
        component: AssetCategory,
    },
    {
        path: '/asset-item-master',
        name: 'Asset Item Master',
        component: AssetItemMaster,
    },
    {
        path: '/asset-monitoring',
        name: 'Asset Monitoring',
        component: AssetMonitoring,
    },
    {
        path: '/asset-monitoring/bulkPrint/print',
        name: 'print-asset',
        component: PrintAsset,
    },
  {
        path: '/pallet-monitoring',
        name: 'Pallet Monitoring',
        component: PalletMonitoring,
    },
  {
        path: '/usersite',
        name: 'User Site',
        component: UserSite,
    },
        // job order request
    {
        path: '/job-order-request-list',
        name: 'Job Order Request',
        component: joborder,

    },
    {
        path: '/job-order-request-list/create',
        name: 'Job Order Create',
        component: JobOrderAction,
    },
    {
        path: '/job-order-request-approved/:id/view',
        name: 'Job-Order-Request-Approved',
        component: JobOrderRequestApproved,
    },
    // Mrf

          {
            path: '/Marial-Requisition',
            name: 'Material Requisition Form List',
            component: MrfList,

        },
        {
            path: '/Marial-Requisition/create',
            name: 'Create Material Requisition Form',
            component: MrfAction,
        },
        {
            path: '/Marial-Requisition-approved/:id/view',
            name: 'marial-Requisition-Approved',
            component: MrfRequestApproved,
        },
]

