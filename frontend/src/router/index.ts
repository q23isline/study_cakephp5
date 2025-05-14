import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/users',
      name: 'userList',
      component: () => import('../views/users/UserListView.vue'),
    },
    {
      path: '/users/add',
      name: 'userAdd',
      component: () => import('../views/users/UserAddView.vue'),
    },
    {
      path: '/users/:userId(\\d+)',
      name: 'user',
      component: () => import('../views/users/UserView.vue'),
      props: (route) => ({ userId: Number(route.params.userId) }),
    },
    {
      path: '/users/edit/:userId(\\d+)',
      name: 'userEdit',
      component: () => import('../views/users/UserEditView.vue'),
      props: (route) => ({ userId: Number(route.params.userId) }),
    },
    {
      path: '/:notFound(.*)',
      name: 'NotFoundError',
      component: () => import('../views/errors/NotFoundErrorView.vue'),
    },
  ],
})

export default router
