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
import IndexBREX from './components/subComponents/IndexBREX.vue';

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
  },
  {
    name: 'DetailDML',
    path: '/csdb3/dml/:filename',
    component: DetailDML
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
    name: 'Committing-IndexDML',
    path: '/csdb3/commit-indexDML',
    component: IndexDML,
    props: {isInEditing: false}
  },
  {
    name: 'Committing-IndexBREX',
    path: '/csdb3/commit-indexBREX',
    component: IndexBREX,
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