import Project from './components/route/Project.vue';
import ProjectDetail from './components/route/ProjectDetail.vue';
import ObjectUpdate from './components/route/ObjectUpdate.vue';
import ObjectDetail from './components/route/ObjectDetail.vue';

export default [
  {
    name: 'Project',
    path: '/ms/project',
    component: Project
  },
  {
    name: 'ProjectDetail',
    path: '/ms/project/:projectName',
    component: ProjectDetail,
    props: true,
  },
  {
    name: 'ObjectDetail',
    path: '/ms/:projectName/:filename',
    component: ObjectDetail,
    props: true,
  },
  {
    name: 'ObjectUpdate',
    path: '/ms/:projectName/:filename/update',
    component: ObjectUpdate,
  },

];