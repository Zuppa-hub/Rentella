import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginVue from '../views/Login.vue'
import OrderView from '../views/OrderView.vue'
import HistoryView from '../views/HistoryView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'Home',
      component: HomeView
    },
    {
      path: '/Login',
      name: 'Login',
      component: LoginVue
    },
    {
      path: '/Order',
      name: 'Order',
      component: OrderView
    },
    {
      path: '/History',
      name: 'History',
      component: HistoryView
    },
  ]
})

export default router
