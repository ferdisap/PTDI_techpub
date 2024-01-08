// import App from './App.vue';
import BrexDetail from './components/route/BrexDetail.vue';

export default [
  {
    name: 'BrexDetail',
    path: '/brex2/:projectName/:filename',
    component: BrexDetail,
  },
]