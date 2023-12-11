import ListObject from './components/list-object.vue';
import InsertToken from './components/insert-token.vue';
import ListRepo from './components/list-repo.vue';
import Index from './index.vue';
import Content from './components/content.vue';

export default [
  {
    path: '/ietm',
    name: 'Index',
    component: Index
  },
  {
    path: '/ietm/InsertToken',
    name: 'InsertToken',
    component: InsertToken,
  },
  {
    name: 'ListRepo',
    path: '/ietm/ListRepo',
    component: ListRepo,
  },
  // {
  //   name: 'ListObject',
  //   path: '/ietm/Content/:repoName',
  //   component: ListObject,
  // },
  {
    name: 'ListObject',
    path: '/ietm/Content/:repoName',
    component: Content,
  },
  {
    name: 'Content',
    path: '/ietm/Content',
    component: Content,
  },
  {
    name: 'Detail',
    path: '/ietm/Content/:repoName/:filename',
    component: Content,
  },
]