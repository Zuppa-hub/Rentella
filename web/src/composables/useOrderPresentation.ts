import type { Order } from '../services/api'

export const getOrderBeachName = (order: Order): string => {
  return order.umbrella?.zone?.beach?.name || 'Unknown Beach'
}

export const getOrderCityName = (order: Order): string => {
  return order.umbrella?.zone?.beach?.city_location?.city_name || 'Unknown City'
}

export const getOrderZoneName = (order: Order): string => {
  return order.umbrella?.zone?.name || 'Unknown Zone'
}

export const getOrderUmbrellaNumber = (order: Order): string => {
  return order.umbrella?.number != null ? String(order.umbrella.number) : '-'
}
