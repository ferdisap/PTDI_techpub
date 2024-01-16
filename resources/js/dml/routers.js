import Dml from './components/route/Dml.vue';
import Create from './components/route/Create.vue';
import DmlEntry from './components/route/DmlEntry.vue';
export default [
  {
    name: 'Dml',
    path: '/dml',
    component: Dml
  },
  {
    name: 'Create',
    path: "/dml/create",
    component: Create
  },
  {
    name: 'Entry',
    path: "/dml/entry",
    component: DmlEntry
  },

];