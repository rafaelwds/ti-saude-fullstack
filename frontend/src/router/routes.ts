import type { RouteRecordRaw } from 'vue-router';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', redirect: '/login' },
      {
        path: 'login',
        component: () => import('pages/LoginPage.vue'),
        meta: { requiresAuth: false },
      },
      {
        path: 'pacientes',
        component: () => import('pages/PacientesPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'especialidades',
        component: () => import('pages/EspecialidadesPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'medicos',
        component: () => import('pages/MedicosPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'consultas',
        component: () => import('pages/ConsultasPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'procedimentos',
        component: () => import('pages/ProcedimentosPage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'planos-saude',
        component: () => import('pages/PlanosSaudePage.vue'),
        meta: { requiresAuth: true },
      },
      {
        path: 'vinculos',
        component: () => import('pages/VinculosPage.vue'),
        meta: { requiresAuth: true },
      },
    ],
  },

  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue'),
  },
];

export default routes;
