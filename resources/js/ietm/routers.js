import ListObject from './components/subcomponents/list-object.vue';
import InsertToken from './components/subcomponents/insert-token.vue';

export default [
  {
    path: '/ietm',
    name: 'index',
    component: InsertToken
  },
  {
    name: 'ListObject',
    path: '/ietm/list-object',
    component: ListObject,
    props: true
  }
]