import Project from './components/route/Project.vue';
import ProjectDetail from './components/route/ProjectDetail.vue';
import ObjectUpdate from './components/route/ObjectUpdate.vue';
export default [
  {
    name: 'Project',
    path: '/ms/project',
    component: Project
  },
  {
    name: 'ProjectDetail',
    path: '/ms/project/:projectName',
    component: ProjectDetail
  },
  {
    name: 'ObjectDetail',
    path: '/ms/:projectName/:filename',
    component: ProjectDetail // nanti diganti
  },
  {
    name: 'ObjectUpdate',
    path: '/ms/:projectName/:filename/update',
    component: ObjectUpdate,
  },

];