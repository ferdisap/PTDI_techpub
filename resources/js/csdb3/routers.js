import Committing from './route/Committing.vue';
import Staging from './route/Staging.vue';
import Editting from './route/Editting.vue';
import Welcome from './route/Welcome.vue';
import DetailDML from './components/subComponents/DetailDML.vue';
import DetailBREX from './components/subComponents/DetailBREX.vue';
import DetailObject from './components/subComponents/DetailObject.vue';

export default [
  {
    name: 'Welcome',
    path: '/csdb3',
    component: Welcome
  },

  // commit
  {
    name: 'Commit',
    path: '/csdb3/commit',
    component: Committing
  },
  {
    name: 'DetailDML',
    path: '/csdb3/commit/:filename',
    component: DetailDML
  },
  {
    name: 'DetailBREX',
    path: '/csdb3/commit/:filename',
    component: DetailBREX
  },

  // stage
  {
    name: 'Stage',
    path: '/csdb3/stage',
    component: Staging
  },

  // edit
  {
    name: 'Edit',
    path: '/csdb3/edit',
    component: Editting
  },
  {
    name: 'DetailObject',
    path: '/csdb3/edit/:filename',
    component: DetailObject
  },
  {
    name: 'DetailDMLEDIT',
    path: '/csdb3/edit-dml/:filename',
    component: DetailDML,
    props: {isInEditing: true}
  },
  {
    name: 'DetailCSLEDIT',
    path: '/csdb3/edit-csl/:filename',
    component: DetailDML,
    props: {isCSL: true}
  },

];