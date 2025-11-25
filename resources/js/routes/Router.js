import { createRouter, createWebHistory } from "vue-router";

// import calendar from '../calendar/calendar.vue';
// import myRequest from '../request/index.vue';
// import myHistory from '../leave-history/index.vue';
import login from '../page/auth/login.vue';
import chart from '../page/components/charts/index.vue';

const routes = [
	{
		path: '/login',
		name: 'login',
		component: login
	},
	{
		path: '/chart',
		name: 'chart',
		component: chart,
		// children: [
		// 	{
		// 		path: '/chart',
		// 		name: 'chart',
		// 		component: chart
		// 	},
			// {
			// 	path: '/my-request',
			// 	name: 'my-request',
			// 	component: myRequest
			// },
			// {
			// 	path: '/my-history',
			// 	name: 'my-history',
			// 	component: myHistory
			// }
		// ]
	}
]
export default createRouter({
	history: createWebHistory(),
	routes
})