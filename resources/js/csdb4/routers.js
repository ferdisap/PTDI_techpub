import Welcome from './components/route/Welcome.vue'
import Explorer from './components/route/Explorer.vue'

export default [
  {
    name: 'Welcome',
    path: '/csdb4',
    component: Welcome
  },
  {
    name: 'Explorer',
    path: '/csdb4/explorer',
    component: Explorer
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