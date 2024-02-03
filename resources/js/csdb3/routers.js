import Committing from './route/Committing.vue';
import Staging from './route/Staging.vue';
import Editting from './route/Editting.vue';
import Welcome from './route/Welcome.vue';
import DetailDML from './components/subComponents/DetailDML.vue';
import DetailBREX from './components/subComponents/DetailBREX.vue';
import DetailObject from './components/subComponents/DetailObject.vue';
import IndexDML from './components/subComponents/IndexDML.vue';
import Upload from './components/subComponents/Upload.vue';
import Editor from './components/subComponents/Editor.vue';
import IndexObject from './components/subComponents/IndexObject.vue';
import PushToStage from './components/subComponents/PushToStage.vue';
import IndexBR from './components/subComponents/IndexBR.vue';
import CreateDML from './components/subComponents/CreateDML.vue';

export default [
  {
    name: 'Welcome',
    path: '/csdb3',
    component: Welcome
  },
  {
    name: 'IndexObject',
    path: '/csdb3/IndexObject',
    component: IndexObject,
    props: {filter: ''}
  },
  {
    name: 'InEditting-IndexObject',
    path: '/csdb3/InEditting-IndexObject',
    component: IndexObject,
    props: {filter: 'inEditting'}
  },
  {
    name: 'DetailDML',
    path: '/csdb3/dml/:filename',
    component: DetailDML
  },
  {
    name: 'IndexBR',
    path: '/csdb3/indexBR',
    component: IndexBR,
  },
  {
    name: 'CreateBR',
    path: '/csdb3/createBR',
    component: Editor,
    props:{routeNameCreate: 'api.create_br', title: 'Create Business Rule'}
  },
  {
    name: 'CreateDML',
    path: '/csdb3/createDML',
    component: CreateDML,
  },

  // commit
  {
    name: 'Commit',
    path: '/csdb3/commit',
    component: Committing
  },
  {
    name: 'DetailBREX',
    path: '/csdb3/commit/:filename',
    component: DetailBREX
  },
  {
    name: 'IndexDML',
    path: '/csdb3/indexDML',
    component: IndexDML,
    props: {isInEditing: false}
  },

  // stage
  {
    name: 'Stage',
    path: '/csdb3/stage',
    component: Staging
  },

  // edit
  // {
  //   name: 'Edit',
  //   path: '/csdb3/edit',
  //   component: Editting
  // },
  {
    name: 'DetailObject',
    path: '/csdb3/edit/:filename',
    component: DetailObject
  },
  {
    name: 'DetailDML_inEditing',
    path: '/csdb3/inediting/:filename',
    component: DetailDML,
    props: {isInEditing: true}
  },
  {
    name: 'Editing-IndexDML',
    path: '/csdb3/edit-indexDML',
    component: IndexDML,
    props: {isInEditing: true}
  },
  {
    name: 'Editing-Upload',
    path: '/csdb3/edit-upload',
    component: Upload,
  },
  {
    name: 'Editing-Editor',
    path: '/csdb3/edit-editor',
    component: Editor,
  },
  {
    name: 'Editing-PushToStage',
    path: '/csdb3/edit-pushtostage',
    component: PushToStage,
  },
];