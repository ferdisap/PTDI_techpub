import ListObject from './components/list-object.vue';
import InsertToken from './components/insert-token.vue';
import ListRepo from './components/list-repo.vue';
import Content from './components/content.vue';
// import Body from './components/body.vue';
import Index from './components/Index.vue'

export default [
  {
    name: 'ListRepo',
    path: '/ietm/list-repo',
    component: ListRepo,
  },
  {
    name: 'ListObject',
    path: '/ietm/content/:repoName',
    component: Content,
  },
  {
    name: 'Content',
    path: '/ietm/content',
    component: Content,
  },
  {
    name: 'Detail',
    path: '/ietm/content/:repoName/:filename',
    component: Content,
  },
  {
    path: '/ietm/insert-token',
    name: 'InsertToken',
    component: InsertToken,
  },
  {
    path: '/ietm',
    name: 'Index',
    component: Index
  },
]