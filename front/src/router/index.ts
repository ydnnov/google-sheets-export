import { createRouter, createWebHistory } from 'vue-router';
import entries from '@/router/entries.ts';

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      redirect: { name: 'entry.list' },
    },
    ...entries,
  ],
});

export default router;
