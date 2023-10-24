import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginVue from '../views/Login.vue'
import OrderView from '../views/OrderView.vue'
import HistoryView from '../views/HistoryView.vue'
import BeachSelectionViewVue from '../views/BeachSelectionView.vue'

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
    {
      path: '/BeachSelection/:id',
      name: 'BeachSelection',
      component: BeachSelectionViewVue,
      props: (route) => ({
        id: route.params.id,       // Ricevi il parametro 'id'
        cityName: route.query.cityName, // Ricevi il parametro 'param1' dalla query string
        distance: route.query.distance, // Ricevi il parametro 'param2' dalla query string
      })
    },
  ]
})

export default router
