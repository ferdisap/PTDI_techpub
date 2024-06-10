import Welcome from './components/route/Welcome.vue';
import Explorer from './components/route/Explorer.vue';
import Deletion from './components/route/Deletion.vue';

export default [
  {
    name: 'Welcome',
    path: '/csdb4',
    component: Welcome
  },
  // {
  //   name: 'Explorer',
  //   path: '/csdb4/explorer',
  //   component: Explorer
  // },
  {
    name: 'Explorer',
    path: '/csdb4/explorer/:filename?/:viewType?',
    component: Explorer
  },
  {
    name: 'Deletion',
    path: '/csdb4/deletion',
    component: Deletion
  },
  // {
  //   name: 'Staging',
  //   path: '/csdb3/staging',
  //   component: Staging
  // },
  // {
  //   name: 'Deletion',
  //   path: '/csdb3/deletion',
  //   component: Deletion,
  // },
];