import Welcome from './components/route/Welcome.vue';
import Explorer from './components/route/Explorer.vue';
import Deletion from './components/route/Deletion.vue';
import Dispatch from './components/route/Dispatch.vue';

export default [
  {
    name: 'Welcome',
    path: '/csdb4',
    component: Welcome
  },
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
  {
    name: 'Dispatch',
    path: '/csdb4/dispatch/:filename?',
    component: Dispatch
  },
];