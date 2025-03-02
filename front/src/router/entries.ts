export default [
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
];