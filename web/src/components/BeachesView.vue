<template>
  <div class="beaches-view" :class="{ 'checkout-mode': isZonePickerOpen }">
    <template v-if="!isZonePickerOpen">
      <BeachesListContent
        :model-value="searchTerm"
        :location="location"
        :beaches="beaches"
        :filtered-beaches="filteredBeaches"
        :expanded-beach-id="expandedBeachId"
        :zones-by-beach="zonesByBeach"
        :loading-zones-by-beach="loadingZonesByBeach"
        :zones-error-by-beach="zonesErrorByBeach"
        :beach-types="beachTypes"
        :icons="icons"
        @update:model-value="searchTerm = $event"
        @back="handleTopBack"
        @select-beach="handleSelectBeach"
        @photo-click="handlePhotoClick"
        @zone-select="handleZoneSelect($event.zone, $event.beach)"
      />
    </template>

    <BeachesZonePicker
      v-else
      :is-desktop="false"
      :selected-zone-beach="selectedZoneBeach"
      :selected-zone="selectedZone"
      :reservation-name="reservationName"
      :reservation-from="reservationFrom"
      :reservation-to="reservationTo"
      :today-date="todayDate"
      :selected-zone-price="selectedZonePrice"
      :zone-daily-price="zoneDailyPrice"
      :formatted-duration="formattedDuration"
      :zone-picker-step="zonePickerStep"
      :checkout-feedback="checkoutFeedback"
      :is-submitting-checkout="isSubmittingCheckout"
      :is-checkout-valid="isCheckoutValid"
      :primary-action-label="primaryActionLabel"
      :order-id="orderId"
      :location-name="location.name"
      @update:reservation-name="reservationName = $event"
      @update:reservation-from="reservationFrom = $event"
      @update:reservation-to="reservationTo = $event"
      @back="handleZonePickerBack"
      @primary-action="handlePrimaryAction"
    />
  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { checkZoneAvailability, createZoneOrder, getBeach, type Beach } from '../services/api'
import BeachesListContent from './beaches/BeachesListContent.vue'
import BeachesZonePicker from './beaches/BeachesZonePicker.vue'
import allowedAnimalsIcon from '../assets/icons/AllowedAnimals.svg'
import beachTypeIcon from '../assets/icons/BeachType.svg'
import distanceIcon from '../assets/icons/Distance.svg'
import moneyIcon from '../assets/icons/Money.svg'
import { calculateBookingDays, getCompactTodayDate, getTodayIsoDate } from '../utils/date'
import type { BeachViewModel, BeachZoneViewModel, Location } from '../types/beaches'

const { t } = useI18n()

type BeachZoneSource = {
  id: number
  name?: string | null
  description?: string | null
  umbrellas?: Array<unknown>
  prices?: {
    id?: number | null
    price?: number | null
  } | null
  price_id?: number | null
}

const props = defineProps<{
  location: Location
  beaches: BeachViewModel[]
  expandBeachId?: number | null
  beachTypes?: Record<number, string>
}>()

const emit = defineEmits<{
  back: []
  'select-beach': [beach: BeachViewModel]
}>()

const searchTerm = ref('')
const expandedBeachId = ref<number | null>(null)
const zonesByBeach = ref<Record<number, BeachZoneViewModel[]>>({})
const loadingZonesByBeach = ref<Record<number, boolean>>({})
const zonesErrorByBeach = ref<Record<number, string>>({})
const isZonePickerOpen = ref(false)
const selectedZone = ref<BeachZoneViewModel | null>(null)
const selectedZoneBeach = ref<BeachViewModel | null>(null)
const reservationName = ref('')
const reservationFrom = ref('')
const reservationTo = ref('')
const checkoutFeedback = ref<{ type: 'success' | 'error' | 'info'; message: string } | null>(null)
const isSubmittingCheckout = ref(false)
const zonePickerStep = ref<'form' | 'summary' | 'payment' | 'success'>('form')
const selectedPriceId = ref<number | null>(null)
const orderId = ref<string | null>(null)

const todayDate = getTodayIsoDate()

const generateOrderId = (): string => {
  const date = getCompactTodayDate()
  const random = Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, '0')
  return `ORD-${date}-${random}`
}

const resetReservationForm = () => {
  reservationName.value = ''
  reservationFrom.value = todayDate
  reservationTo.value = todayDate
  zonePickerStep.value = 'form'
  selectedPriceId.value = null
  checkoutFeedback.value = null
}

const filteredBeaches = computed(() => {
  if (!searchTerm.value) return props.beaches
  const query = searchTerm.value.toLowerCase()
  return props.beaches.filter((beach) => beach.name.toLowerCase().includes(query))
})

const handleBack = () => {
  emit('back')
}

const handleTopBack = () => {
  if (isZonePickerOpen.value) {
    closeZonePicker()
    return
  }
  handleBack()
}

const handlePhotoClick = (beach: BeachViewModel) => {
  emit('select-beach', beach)
}

const handleZoneSelect = (zone: BeachZoneViewModel, beach: BeachViewModel) => {
  selectedZone.value = zone
  selectedZoneBeach.value = beach
  isZonePickerOpen.value = true
  resetReservationForm()
}

const closeZonePicker = () => {
  isZonePickerOpen.value = false
  selectedZone.value = null
  selectedZoneBeach.value = null
  resetReservationForm()
}

const calculateDays = (): number => {
  if (!reservationFrom.value || !reservationTo.value) return 0
  return calculateBookingDays(reservationFrom.value, reservationTo.value)
}

const selectedZonePrice = computed(() => {
  if (!selectedZone.value || selectedZone.value.price === null) return '-'
  const dailyPrice = selectedZone.value.price
  const days = calculateDays()
  const totalPrice = dailyPrice * days
  return `${totalPrice} €`
})

const zoneDailyPrice = computed(() => {
  if (!selectedZone.value || selectedZone.value.price === null) return '-'
  return `${selectedZone.value.price} €/giorno`
})

const formattedDuration = computed(() => {
  if (!reservationFrom.value || !reservationTo.value) return '-'
  const from = reservationFrom.value.split('-').reverse().join('.')
  const to = reservationTo.value.split('-').reverse().join('.')
  return `${from}-${to}`
})

const primaryActionLabel = computed(() => {
  if (zonePickerStep.value === 'success') return t('desktop.zonePicker.newReservation')
  if (zonePickerStep.value === 'payment') return t('desktop.zonePicker.payment')
  return zonePickerStep.value === 'summary'
    ? t('desktop.zonePicker.payment')
    : t('desktop.zonePicker.checkout')
})

const isCheckoutValid = computed(() => {
  if (zonePickerStep.value === 'success') {
    return true
  }
  if (zonePickerStep.value === 'payment') {
    return !isSubmittingCheckout.value
  }
  if (zonePickerStep.value === 'summary') {
    return !isSubmittingCheckout.value
  }
  const hasName = reservationName.value.trim().length > 0
  const hasDates = Boolean(reservationFrom.value) && Boolean(reservationTo.value)
  const isDateRangeValid = reservationFrom.value <= reservationTo.value
  return Boolean(
    selectedZone.value &&
    selectedZoneBeach.value &&
    hasName &&
    hasDates &&
    isDateRangeValid &&
    !isSubmittingCheckout.value
  )
})

const handleCheckout = async () => {
  if (!selectedZone.value || !selectedZoneBeach.value) return
  checkoutFeedback.value = {
    type: 'info',
    message: t('desktop.zonePicker.checkingAvailability'),
  }
  isSubmittingCheckout.value = true

  try {
    const availability = await checkZoneAvailability({
      zoneId: selectedZone.value.id,
      startDate: reservationFrom.value,
      endDate: reservationTo.value,
    })

    if (!availability.available) {
      checkoutFeedback.value = {
        type: 'error',
        message: t('desktop.zonePicker.notAvailable'),
      }
      return
    }

    selectedPriceId.value = selectedZone.value.priceId ?? availability.price_id
    zonePickerStep.value = 'summary'
    checkoutFeedback.value = null
  } catch {
    checkoutFeedback.value = {
      type: 'error',
      message: t('desktop.zonePicker.orderError'),
    }
  } finally {
    isSubmittingCheckout.value = false
  }
}

const handlePayment = async () => {
  if (!selectedZone.value || !selectedZoneBeach.value) return

  isSubmittingCheckout.value = true
  checkoutFeedback.value = {
    type: 'info',
    message: t('desktop.zonePicker.paymentProcessing'),
  }

  // Simulate payment gateway processing delay
  await new Promise((resolve) => setTimeout(resolve, 2000))

  try {
    const checkoutResult = await createZoneOrder({
      zoneId: selectedZone.value.id,
      startDate: reservationFrom.value,
      endDate: reservationTo.value,
      priceId: selectedPriceId.value,
    })

    orderId.value = String(checkoutResult.order.id ?? generateOrderId())

    // Keep local cache in sync with persisted backend order
    const newOrder = {
      id: orderId.value,
      beachName: selectedZoneBeach.value?.name || '',
      zoneName: selectedZone.value?.name || '',
      location: props.location.name,
      checkInDate: reservationFrom.value,
      checkOutDate: reservationTo.value,
      totalPrice: selectedZonePrice.value,
      createdAt: Date.now(),
      status: 'active' as const,
    }

    const existingOrders = localStorage.getItem('rentella_orders')
    const orders = existingOrders ? JSON.parse(existingOrders) : []
    orders.unshift(newOrder)
    localStorage.setItem('rentella_orders', JSON.stringify(orders))

    // Move to success step
    zonePickerStep.value = 'success'
    checkoutFeedback.value = null
  } catch {
    checkoutFeedback.value = {
      type: 'error',
      message: t('desktop.zonePicker.orderError'),
    }
  } finally {
    isSubmittingCheckout.value = false
  }
}

const handlePrimaryAction = () => {
  if (zonePickerStep.value === 'success') {
    closeZonePicker()
    return
  }
  if (zonePickerStep.value === 'payment') {
    void handlePayment()
    return
  }
  if (zonePickerStep.value === 'summary') {
    zonePickerStep.value = 'payment'
    return
  }
  void handleCheckout()
}

const handleZonePickerBack = () => {
  if (zonePickerStep.value === 'success') {
    closeZonePicker()
    return
  }
  if (zonePickerStep.value === 'payment') {
    zonePickerStep.value = 'summary'
    checkoutFeedback.value = null
    return
  }
  if (zonePickerStep.value === 'summary') {
    zonePickerStep.value = 'form'
    checkoutFeedback.value = null
    return
  }
  closeZonePicker()
}

const normalizeZones = (beachDetails: Beach): BeachZoneViewModel[] => {
  const zones: BeachZoneSource[] = Array.isArray(beachDetails?.zones)
    ? (beachDetails.zones as BeachZoneSource[])
    : []

  return zones.map((zone) => ({
    id: zone.id,
    name: zone.name || t('desktop.beach.zoneNumber', { number: zone.id }),
    description: zone.description ? String(zone.description) : null,
    umbrellasCount: zone.umbrellas?.length ?? null,
    price: zone.prices?.price ?? null,
    priceId: zone.prices?.id ?? zone.price_id ?? null,
  }))
}

const loadZonesForBeach = async (beachId: number) => {
  if (zonesByBeach.value[beachId] || loadingZonesByBeach.value[beachId]) return

  loadingZonesByBeach.value[beachId] = true
  zonesErrorByBeach.value[beachId] = ''

  try {
    const beachDetails = await getBeach(beachId)
    zonesByBeach.value[beachId] = normalizeZones(beachDetails)
  } catch (err) {
    zonesErrorByBeach.value[beachId] = err instanceof Error ? err.message : t('desktop.beach.zonesLoadError')
  } finally {
    loadingZonesByBeach.value[beachId] = false
  }
}

const expandBeach = async (beach: BeachViewModel, force: boolean = false) => {
  if (!force && expandedBeachId.value === beach.id) {
    expandedBeachId.value = null
    return
  }

  expandedBeachId.value = beach.id
  await loadZonesForBeach(beach.id)
}

const handleSelectBeach = (beach: BeachViewModel) => {
  void expandBeach(beach)
}

watch(
  () => props.expandBeachId,
  (beachId) => {
    if (beachId === null || beachId === undefined) return
    const beach = props.beaches.find((item) => item.id === beachId)
    if (beach) {
      void expandBeach(beach, true)
    }
  }
)

watch(
  () => reservationFrom.value,
  (fromDate) => {
    if (!fromDate) return
    if (!reservationTo.value || reservationTo.value < fromDate) {
      reservationTo.value = fromDate
    }
  }
)

const icons = {
  beachType: beachTypeIcon,
  allowedAnimals: allowedAnimalsIcon,
  distance: distanceIcon,
  money: moneyIcon,
}
</script>

<style scoped>
.beaches-view {
  width: 100%;
  flex: 1;
  margin-top: -67px;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  font-family: 'Inter', sans-serif;
  border-radius: 32px 32px 0 0;
  padding: 20px clamp(12px, 4vw, 32px) calc(100px + env(safe-area-inset-bottom));
  box-shadow: 0 -2px 8px rgba(85, 85, 102, 0.18);
  min-height: calc(100vh - 80px - (64px + 204px - 67px));
  max-height: calc(100vh - 80px - (64px + 204px - 67px));
  box-sizing: border-box;
  position: relative;
  z-index: 99;
  overflow: hidden;
}

.beaches-view.checkout-mode {
  position: relative;
  margin-top: 0;
  min-height: 100%;
  max-height: 100%;
  height: 100%;
  width: 100%;
  flex: 1;
  border-radius: 0;
  padding: 0;
  box-shadow: none;
  background: #fafafc;
}

/* Desktop styling - sidebar layout */
@media (min-width: 1024px) {
  .beaches-view {
    width: 500px;
    min-width: 500px;
    max-width: 500px;
    flex: 0 0 500px;
    background: #fafafc;
    border-radius: 0 20px 20px 0;
    box-shadow: 8px 0px 8px rgba(136, 136, 136, 0.16);
    padding: 32px 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin: 0;
    margin-top: 0;
    overflow: hidden;
    z-index: 2;
    order: 1;
    height: auto;
    min-height: 0;
    max-height: none;
  }

  .beaches-view.checkout-mode {
    height: 100%;
    min-height: 0;
    max-height: none;
    width: 100%;
    background: transparent;
  }
}

@media (max-width: 1023px) {
  .beaches-view.checkout-mode {
    position: fixed;
    top: calc(var(--topbar-height, 64px) + env(safe-area-inset-top));
    right: 0;
    bottom: calc(var(--nav-height, 80px) + env(safe-area-inset-bottom));
    left: 0;
    z-index: 1200;
    width: 100%;
    height: auto;
    min-height: 0;
    max-height: none;
    margin: 0;
    border-radius: 0;
    padding: 0;
  }
}
</style>
