import './bootstrap';
import {createApp} from 'vue';

import App from './page/App.vue';
import Router from './routes/Router';
import load from '@/windowLoad/load';
import { Chart, registerables } from 'chart.js';
import { Bar, Line, Pie, Doughnut } from 'vue-chartjs';

Chart.register(...registerables);
const app = createApp(App).use(Router);

app.component('BarChart', Bar);
app.component('LineChart', Line);
app.component('PieChart', Pie);
app.component('DoughnutChart', Doughnut);

function importCommonComponents() {
    let context = import.meta.glob('./page/components/*.vue', { eager: true });
    for (const key of Object.keys(context)) {
        const module = context[key].default;
        const name = key.split('/').pop().split('.')[0];
      app.component(name, module);
    }
  }
importCommonComponents();
// const infor = JSON.parse(sessionStorage.getItem('inforUser'));
// if (infor) {
//     window.axios.defaults.headers.common["Authorization"] = `${infor.token_type} ${infor.access_token}`;
// } else {
//   Router.push({ path: '/login' })
// }
window.addEventListener('load', load, false);
app.config.globalProperties.$Chart = Chart;
app.mount("#app");
