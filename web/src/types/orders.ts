import type { Order } from '../services/api'

export interface ActiveOrderView {
  id: number
  raw: Order
  beachName: string
  cityName: string
  zoneName: string
  umbrellaNumber: string
  price: string
  startDateFormatted: string
  isCancellable: boolean
}

export interface OrderDetailView {
  id: number
  beachName: string
  cityName: string
  zoneName: string
  umbrellaNumber: string
  checkInFormatted: string
  checkOutFormatted: string
  totalPrice: string
}
