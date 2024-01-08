import Project from './components/route/Project.vue';
import Repository from './components/route/Repository.vue';
import ProjectDetail from './components/route/ProjectDetail.vue';
import ObjectUpdate from './components/route/ObjectUpdate.vue';
import ObjectDetail from './components/route/ObjectDetail.vue';

export default [
  {
    name: 'Project',
    path: '/csdb/project',
    component: Project
  },
  {
    name: 'Repository',
    path: '/csdb/repository',
    component: Repository
  },
  {
    name: 'ProjectDetail',
    path: '/csdb/project/:projectName',
    component: ProjectDetail,
    props: true,
  },
  {
    name: 'ObjectDetail',
    path: '/csdb/:projectName/:filename',
    component: ObjectDetail,
    props: true,
  },
  {
    name: 'ObjectUpdate',
    path: '/csdb/:projectName/:filename/update',
    component: ObjectUpdate,
    props: true,
  },

];