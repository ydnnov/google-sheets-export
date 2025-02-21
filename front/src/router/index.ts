import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      redirect: { name: 'entry.list' },
    },
    {
      path: '/entries',
      name: 'entry.list',
      component: () => import('../views/EntriesListView.vue'),
      children: [
        {
          path: 'new',
          name: 'entry.create',
          component: () => import('../views/EntryFormView.vue'),
        },
        {
          path: ':id',
          name: 'entry.edit',
          component: () => import('../views/EntryFormView.vue'),
        },
      ],
    },
  ],
});

export default router;
