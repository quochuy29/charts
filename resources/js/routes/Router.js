import { createRouter, createWebHistory } from "vue-router";

// import calendar from '../calendar/calendar.vue';
// import myRequest from '../request/index.vue';
// import myHistory from '../leave-history/index.vue';
import login from '../page/auth/login.vue';
import EnergyDashboard from '../page/views/EnergyDashboard.vue';
import ConfigurationLayout from '../page/views/ConfigurationLayout.vue';
import UserManagement from '../page/components/configuration/UserManagement.vue';
import TreeTagConfig from '../page/components/configuration/TreeTagConfig.vue';
import SystemSetting from '../page/components/configuration/SystemSetting.vue';

const routes = [
	{
		path: '/configuration',
		component: ConfigurationLayout,
		children: [
			{
				path: '', // Mặc định chuyển hướng vào User Management
				redirect: { name: 'user-management' }
			},
			{
				path: 'user-management',
				name: 'user-management',
				component: UserManagement
			},
			{
				path: 'tree-tag',
				name: 'tree-tag',
				component: TreeTagConfig
			},
			{
				path: 'setting',
				name: 'setting',
				component: SystemSetting
			}
		]
	},
	{
		path: '/',
		name: 'dashboard',
		component: EnergyDashboard
	},
	{
		path: '/login',
		name: 'login',
		component: login
	}
]
export default createRouter({
	history: createWebHistory(),
	routes
})