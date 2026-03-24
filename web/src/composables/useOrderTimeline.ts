import { computed, type Ref } from 'vue'
import type { Order } from '../services/api'
import { calculateBookingDays } from '../utils/date'

export const parseOrderDate = (value: string): Date | null => {
  if (!value) return null

  const nativeDate = new Date(value)
  if (!Number.isNaN(nativeDate.getTime())) {
    return nativeDate
  }

  const match = value.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/)
  if (!match) return null

  const [_, yearStr, monthStr, dayStr, hourStr = '00', minuteStr = '00', secondStr = '00'] = match

  const year = Number(yearStr)
  const monthIndex = Number(monthStr) - 1
  const day = Number(dayStr)
  const hour = Number(hourStr)
  const minute = Number(minuteStr)
  const second = Number(secondStr)

  const date = new Date(year, monthIndex, day, hour, minute, second)
  return Number.isNaN(date.getTime()) ? null : date
}

export const useOrderTimeline = (orders: Ref<Order[]>) => {
  const activeOrders = computed(() => {
    const now = new Date()
    return orders.value.filter((order) => {
      const endDate = parseOrderDate(order.end_date)
      if (!endDate) return false
      return endDate > now
    })
  })

  const finishedOrders = computed(() => {
    const now = new Date()
    return orders.value.filter((order) => {
      const endDate = parseOrderDate(order.end_date)
      if (!endDate) return false
      return endDate < now
    })
  })

  return {
    activeOrders,
    finishedOrders,
  }
}

export const formatOrderDate = (dateStr: string): string => {
  const parsed = parseOrderDate(dateStr)
  if (!parsed) return dateStr

  const day = String(parsed.getDate()).padStart(2, '0')
  const month = String(parsed.getMonth() + 1).padStart(2, '0')
  const year = parsed.getFullYear()
  return `${day}.${month}.${year}`
}

export const formatOrderTotalPrice = (order: Pick<Order, 'start_date' | 'end_date' | 'price'>): string => {
  const price = order.price?.price
  if (typeof price !== 'number') return 'N/A'

  const durationDays = calculateBookingDays(order.start_date, order.end_date)
  return `${price * durationDays}€`
}
