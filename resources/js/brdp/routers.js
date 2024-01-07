// import App from './App.vue';
import BrdpDetail from './components/route/BrdpDetail.vue';

export default [
  // {
  //   name: 'App',
  //   path: '/brdp/:projectName/:filename',
  //   component: App,
  // },
  {
    name: 'BrdpDetail',
    path: '/brdp/:projectName/:filename',
    component: BrdpDetail,
  },
]