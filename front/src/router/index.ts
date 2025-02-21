import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
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
