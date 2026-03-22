<template>
  <div class="beaches-view" :class="{ 'checkout-mode': isZonePickerOpen }">
    <!-- Back Button -->
    <div v-if="!isZonePickerOpen" class="back-section">
      <button class="back-button" @click="handleTopBack">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
        {{ t('common.back') }}
      </button>
    </div>

    <template v-if="!isZonePickerOpen">
      <!-- Search -->
      <SearchBox v-model="searchTerm" :placeholder="t('desktop.search.placeholder')" />

      <div class="divider"></div>

      <!-- Header Info -->
      <div class="beaches-header">
        <div class="header-title">
          <h2>{{ location.name }}</h2>
          <span class="distance">
            <img :src="icons.distance" alt="" class="meta-icon" />
            {{ location.distance }} km
          </span>
          <span class="count">{{ beaches.length }} {{ t('common.beaches') }}</span>
        </div>
      </div>

      <!-- Beaches List -->
      <div class="beaches-list">
      <div
        v-for="(beach, idx) in filteredBeaches"
        :key="beach.id"
        class="beach-card"
        :class="{ expanded: expandedBeachId === beach.id }"
      >
        <div class="beach-main" @click="handleSelectBeach(beach)">
          <button class="beach-image" type="button" @click.stop="handlePhotoClick(beach)" :aria-label="beach.name">
            <img
              v-if="beach.photo_url"
              :src="beach.photo_url"
              :alt="beach.name"
              class="beach-photo"
              loading="lazy"
            />
            <div v-else class="image-placeholder">🏖️</div>
            <div class="beach-badge">{{ idx + 1 }}</div>
          </button>

          <div class="beach-info">
            <h3 class="beach-name">{{ beach.name }}</h3>
            <div class="beach-details">
              <span class="detail-item" :title="t('desktop.beach.type')">
                <img :src="icons.beachType" alt="" class="detail-icon" />
                {{ getBeachTypeLabel(beach) }}
              </span>
              <span class="detail-item" :title="t('desktop.beach.animals')">
                <img :src="icons.allowedAnimals" alt="" class="detail-icon" />
                {{ isAnimalsAllowed(beach.allowed_animals) ? t('desktop.beach.yes') : t('desktop.beach.no') }}
              </span>
              <span class="detail-item" v-if="beach.min_price && beach.max_price" :title="t('desktop.beach.priceRange')">
                <img :src="icons.money" alt="" class="detail-icon" />
                €{{ beach.min_price }}-{{ beach.max_price }}
              </span>
            </div>
          </div>

          <div class="expand-icon" :class="{ expanded: expandedBeachId === beach.id }">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
          </div>
        </div>

        <div v-if="expandedBeachId === beach.id" class="zones-section">
          <p v-if="loadingZonesByBeach[beach.id]" class="zones-state">{{ t('desktop.beach.loadingZones') }}</p>
          <p v-else-if="zonesErrorByBeach[beach.id]" class="zones-state error">{{ zonesErrorByBeach[beach.id] }}</p>
          <p v-else-if="!(zonesByBeach[beach.id]?.length)" class="zones-state">{{ t('desktop.beach.noZones') }}</p>

          <div v-else class="zones-list">
            <div
              v-for="zone in zonesByBeach[beach.id]"
              :key="zone.id"
              class="zone-card"
              role="button"
              tabindex="0"
              @click="handleZoneSelect(zone, beach)"
              @keydown.enter.prevent="handleZoneSelect(zone, beach)"
              @keydown.space.prevent="handleZoneSelect(zone, beach)"
            >
              <div class="zone-content">
                <div class="zone-header">
                  <h4 class="zone-name">{{ zone.name }}</h4>
                </div>
                <div class="zone-meta">
                  <span class="detail-item" v-if="zone.price !== null">
                    <img :src="icons.money" alt="" class="detail-icon" />
                    €{{ zone.price }}
                  </span>
                  <span class="detail-item" v-if="zone.umbrellasCount !== null">
                    {{ zone.umbrellasCount }} {{ t('desktop.beach.available') }}
                  </span>
                </div>
                <p v-if="zone.description" class="zone-description">{{ zone.description }}</p>
              </div>
              <div class="zone-arrow">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      </div>
    </template>

    <div v-else class="zone-picker-menu">
      <div class="zone-picker-hero">
        <img v-if="selectedZoneBeach?.photo_url" :src="selectedZoneBeach.photo_url" :alt="selectedZoneBeach.name" class="zone-picker-hero-image" loading="lazy" />
        <div v-else class="zone-picker-hero-placeholder">🏖️</div>
      </div>

      <div class="zone-picker-content">
        <div v-if="zonePickerStep !== 'payment'" class="zone-picker-summary">
          <div class="zone-picker-title-wrap">
            <h3 class="zone-picker-beach-name rt-text-title-sm">{{ selectedZoneBeach?.name }}</h3>
            <span class="zone-picker-zone-name rt-text-muted">{{ selectedZone?.name }}</span>
          </div>
          <span class="zone-picker-price rt-text-title-sm">{{ selectedZonePrice }}</span>
        </div>

        <div v-if="zonePickerStep !== 'payment'" class="zone-picker-divider"></div>

        <div v-if="zonePickerStep === 'form'" class="zone-picker-form">
          <label class="zone-picker-label rt-text-label">{{ t('desktop.zonePicker.name') }}</label>
          <input
            v-model="reservationName"
            type="text"
            class="zone-picker-input"
            :placeholder="t('desktop.zonePicker.namePlaceholder')"
            autocomplete="name"
            maxlength="80"
          />

          <div class="zone-picker-dates-grid">
            <div>
              <label class="zone-picker-label rt-text-label">{{ t('desktop.zonePicker.from') }}</label>
              <div class="zone-picker-date-wrap">
                <input
                  v-model="reservationFrom"
                  type="date"
                  class="zone-picker-input zone-picker-date"
                  :min="todayDate"
                />
                <svg class="zone-picker-date-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                  <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
              </div>
            </div>

            <div>
              <label class="zone-picker-label rt-text-label">{{ t('desktop.zonePicker.to') }}</label>
              <div class="zone-picker-date-wrap">
                <input
                  v-model="reservationTo"
                  type="date"
                  class="zone-picker-input zone-picker-date"
                  :min="reservationFrom || todayDate"
                />
                <svg class="zone-picker-date-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                  <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
              </div>
            </div>
          </div>

          <p class="zone-picker-note rt-text-muted">{{ t('desktop.zonePicker.note') }}</p>
          <p v-if="checkoutFeedback" class="zone-picker-feedback" :class="checkoutFeedback.type">
            {{ checkoutFeedback.message }}
          </p>
        </div>

        <div v-else-if="zonePickerStep === 'payment'" class="zone-payment-form">
          <div class="zone-payment-header">
            <h3 class="zone-payment-title">{{ t('desktop.zonePicker.payment') }}</h3>
            <span class="zone-payment-price">{{ selectedZonePrice }}</span>
          </div>

          <div class="zone-payment-divider"></div>

          <div class="zone-payment-demo-notice" role="note">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>Demo Mode — payment is simulated. No real card data is collected or processed.</span>
          </div>

          <div class="zone-payment-mock-card" aria-hidden="true">
            <span class="zone-payment-mock-card-number">•••• •••• •••• 0000</span>
            <div class="zone-payment-mock-card-row">
              <span class="zone-payment-mock-card-name">DEMO CARDHOLDER</span>
              <span class="zone-payment-mock-card-expiry">12/9999</span>
            </div>
          </div>

          <p v-if="checkoutFeedback" class="zone-picker-feedback" :class="checkoutFeedback.type">
            {{ checkoutFeedback.message }}
          </p>
        </div>

        <div v-else-if="zonePickerStep === 'success'" class="zone-success-page">
          <div class="zone-success-header">
            <svg class="zone-success-icon" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <h3 class="zone-success-title">Reservation Confirmed!</h3>
          </div>

          <div class="zone-success-order-id">
            <span class="zone-success-label">Order ID</span>
            <span class="zone-success-value">{{ orderId }}</span>
          </div>

          <div class="zone-success-divider"></div>

          <div class="zone-success-details">
            <div class="zone-success-row">
              <span class="zone-success-detail-label">Location:</span>
              <span class="zone-success-detail-value">{{ location.name }}</span>
            </div>
            <div class="zone-success-row">
              <span class="zone-success-detail-label">Beach:</span>
              <span class="zone-success-detail-value">{{ selectedZoneBeach?.name }}</span>
            </div>
            <div class="zone-success-row">
              <span class="zone-success-detail-label">Section:</span>
              <span class="zone-success-detail-value">{{ selectedZone?.name }}</span>
            </div>
            <div class="zone-success-row">
              <span class="zone-success-detail-label">Check-in:</span>
              <span class="zone-success-detail-value">{{ reservationFrom }}</span>
            </div>
            <div class="zone-success-row">
              <span class="zone-success-detail-label">Check-out:</span>
              <span class="zone-success-detail-value">{{ reservationTo }}</span>
            </div>
            <div class="zone-success-row zone-success-row-total">
              <span class="zone-success-detail-label">Total Price:</span>
              <span class="zone-success-detail-value">{{ selectedZonePrice }}</span>
            </div>
          </div>

          <p class="zone-success-note">You will receive a confirmation email shortly with all the details.</p>
        </div>

        <div v-else class="zone-checkout-summary">
          <h3 class="zone-checkout-title">{{ t('desktop.zonePicker.checkout') }}</h3>
          <div class="zone-checkout-table">
            <div class="zone-checkout-row">
              <span class="zone-checkout-label">{{ t('desktop.zonePicker.location') }}</span>
              <span class="zone-checkout-value">{{ location.name }}</span>
            </div>
            <div class="zone-checkout-row">
              <span class="zone-checkout-label">{{ t('desktop.zonePicker.beach') }}</span>
              <span class="zone-checkout-value">{{ selectedZoneBeach?.name }}</span>
            </div>
            <div class="zone-checkout-row">
              <span class="zone-checkout-label">{{ t('desktop.zonePicker.section') }}</span>
              <span class="zone-checkout-value">{{ selectedZone?.name }}</span>
            </div>
            <div class="zone-checkout-row">
              <span class="zone-checkout-label">{{ t('desktop.zonePicker.price') }}</span>
              <span class="zone-checkout-value">{{ zoneDailyPrice }}</span>
            </div>
            <div class="zone-checkout-row">
              <span class="zone-checkout-label">{{ t('desktop.zonePicker.duration') }}</span>
              <span class="zone-checkout-value">{{ formattedDuration }}</span>
            </div>
          </div>

          <div class="zone-checkout-total">
            <span>{{ t('desktop.zonePicker.total') }}</span>
            <span>{{ selectedZonePrice }}</span>
          </div>

          <p class="zone-picker-note rt-text-muted">{{ t('desktop.zonePicker.note') }}</p>
          <p v-if="checkoutFeedback" class="zone-picker-feedback" :class="checkoutFeedback.type">
            {{ checkoutFeedback.message }}
          </p>
        </div>
      </div>

      <div class="zone-picker-actions">
        <button class="zone-picker-back-btn rt-btn rt-btn-ghost" type="button" @click="handleZonePickerBack">{{ t('common.back') }}</button>
        <button
          class="zone-picker-checkout-btn rt-btn rt-btn-primary"
          type="button"
          :disabled="!isCheckoutValid"
          @click="handlePrimaryAction"
        >
          {{ isSubmittingCheckout ? t('desktop.zonePicker.processing') : primaryActionLabel }}
        </button>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { checkZoneAvailability, getBeach, type Beach } from '../services/api'
import SearchBox from './SearchBox.vue'
import allowedAnimalsIcon from '../assets/icons/AllowedAnimals.svg'
import beachTypeIcon from '../assets/icons/BeachType.svg'
import distanceIcon from '../assets/icons/Distance.svg'
import moneyIcon from '../assets/icons/Money.svg'
import { isAnimalsAllowed, parseBeachTypeId } from '../utils/helpers'

const { t } = useI18n()

interface Location {
  id: number
  name: string
  lat: number
  lng: number
  distance?: number
  priceRange: string
}

type BeachViewModel = Beach & {
  min_price?: number
  max_price?: number
  has_umbrella?: boolean
  photo_url?: string
}

type BeachZoneViewModel = {
  id: number
  name: string
  description: string | null
  umbrellasCount: number | null
  price: number | null
  priceId: number | null
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

const todayDate = new Date().toISOString().slice(0, 10)

const generateOrderId = (): string => {
  const date = new Date().toISOString().slice(0, 10).replace(/-/g, '')
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

const getBeachTypeLabel = (beach: BeachViewModel) => {
  const parsedId = parseBeachTypeId(beach.type_id)
  if (parsedId === null) {
    return t('desktop.beach.typeUnknown')
  }

  return props.beachTypes?.[parsedId] || t('desktop.beach.typeUnknown')
}

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
  const from = new Date(reservationFrom.value)
  const to = new Date(reservationTo.value)
  const diffTime = Math.abs(to.getTime() - from.getTime())
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
  return Math.max(1, diffDays) // Minimum 1 day
}

const selectedZonePrice = computed(() => {
  if (!selectedZone.value || selectedZone.value.price == null) return '-'
  const dailyPrice = selectedZone.value.price
  const days = calculateDays()
  const totalPrice = dailyPrice * days
  return `${totalPrice} €`
})

const zoneDailyPrice = computed(() => {
  if (!selectedZone.value || selectedZone.value.price == null) return '-'
  return `${selectedZone.value.price} €/giorno`
})

const formattedDuration = computed(() => {
  if (!reservationFrom.value || !reservationTo.value) return '-'
  const from = reservationFrom.value.split('-').reverse().join('.')
  const to = reservationTo.value.split('-').reverse().join('.')
  return `${from}-${to}`
})

const primaryActionLabel = computed(() => {
  if (zonePickerStep.value === 'success') return 'New Reservation'
  if (zonePickerStep.value === 'payment') return t('desktop.zonePicker.payment')
  return zonePickerStep.value === 'summary' ? t('desktop.zonePicker.payment') : t('desktop.zonePicker.checkout')
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
  return Boolean(selectedZone.value && selectedZoneBeach.value && hasName && hasDates && isDateRangeValid && !isSubmittingCheckout.value)
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
    message: 'Processing payment...',
  }

  // Simulate payment processing delay
  await new Promise((resolve) => setTimeout(resolve, 2000))

  try {
    // Simulate API call (in real app, would call createZoneOrder here)
    const generatedOrderId = generateOrderId()
    orderId.value = generatedOrderId

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
  const zones = Array.isArray(beachDetails?.zones) ? beachDetails.zones : []

  return zones.map((zone: any) => ({
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
    if (beachId == null) return
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

/* Back Section */
.back-section {
  padding: 0 0 12px;
}

.back-button {
  background: none;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  color: #414d4f;
  font-size: 14px;
  font-weight: 500;
  padding: 8px;
  transition: color 0.2s ease;
}

.back-button:hover {
  color: #005f6f;
}

/* Search */
.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 14px;
  color: #414d4f;
  outline: none;
}

.search-input::placeholder {
  color: #78898c;
}

.divider {
  height: 4px;
}

/* Beaches Header */
.beaches-header {
  padding: 0;
}

.header-title {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.header-title h2 {
  flex: 1;
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #414d4f;
}

.count {
  font-size: 12px;
  color: #78898c;
  font-weight: 400;
}

.header-meta {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}

.distance {
  font-size: 12px;
  color: #78898c;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.meta-icon {
  width: 12px;
  height: 12px;
  flex-shrink: 0;
  opacity: 0.7;
}

/* Beaches List */
.beaches-list {
  flex: 1;
  overflow-y: auto;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.beach-card {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px;
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 16px;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
  transition: all 0.2s ease;
  cursor: pointer;
}

.beach-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
}

.beach-card.expanded {
  border-color: #c7d5d9;
}

.beach-main {
  display: flex;
  gap: 12px;
  cursor: pointer;
}

/* Beach Image */
.beach-image {
  border: none;
  padding: 0;
  cursor: pointer;
  position: relative;
  width: 80px;
  height: 80px;
  flex-shrink: 0;
  border-radius: 8px;
  overflow: hidden;
  background: #e5e7eb;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
}

.beach-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.beach-badge {
  position: absolute;
  top: 4px;
  left: 4px;
  width: 28px;
  height: 28px;
  background: #ffffff;
  color: #0b0b0b;
  border-radius: 999px;
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  box-shadow: 0 6px 14px rgba(15, 23, 42, 0.08);
}

/* Beach Info */
.beach-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.beach-name {
  font-size: 16px;
  font-weight: 700;
  color: #242b2c;
  margin: 0 0 6px 0;
  letter-spacing: 0.3px;
}

.beach-details {
  display: flex;
  gap: 8px;
  font-size: 13px;
  color: #6b7280;
}

.detail-item {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-right: 12px;
}

.detail-icon {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  opacity: 0.7;
}

.beach-price {
  margin-top: 4px;
}

.price-range {
  font-size: 14px;
  font-weight: 600;
  color: #005f6f;
}

/* Expand Icon */
.expand-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
  transition: color 0.2s ease, transform 0.2s ease;
}

.beach-main:hover .expand-icon {
  color: #005f6f;
}

.expand-icon.expanded {
  transform: rotate(90deg);
  color: #005f6f;
}

.zones-section {
  border-top: 1px solid #e5e7eb;
  padding-top: 10px;
}

.zones-state {
  margin: 4px 0;
  font-size: 13px;
  color: #6b7280;
}

.zones-state.error {
  color: #b42318;
}

.zones-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.zone-card {
  background: #f8fafb;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 10px 12px;
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  gap: 12px;
  align-items: center;
}

.zone-card:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
}

.zone-card:active {
  transform: scale(0.98);
}

.zone-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.zone-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.zone-name {
  margin: 0;
  font-size: 15px;
  color: #242b2c;
}

.zone-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
  transition: color 0.2s ease;
}

.zone-card:hover .zone-arrow {
  color: #005f6f;
}

.zone-meta {
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.zone-description {
  margin: 8px 0 0;
  color: #6b7280;
  font-size: 13px;
  line-height: 1.35;
}

.zone-picker-menu {
  display: flex;
  flex-direction: column;
  height: 100%;
  min-height: 0;
  border-radius: 16px;
  overflow: hidden;
  background: #fafafc;
}

.zone-picker-hero {
  width: 100%;
  height: 168px;
  background: #d6dcde;
}

.zone-picker-hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.zone-picker-hero-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 56px;
}

.zone-picker-content {
  padding: clamp(12px, 2.2vw, 18px);
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
  overflow-y: auto;
}

.zone-picker-summary {
  display: flex;
  justify-content: space-between;
  gap: 8px;
  align-items: baseline;
}

.zone-picker-title-wrap {
  display: flex;
  align-items: baseline;
  gap: 8px;
  min-width: 0;
}

.zone-picker-beach-name {
  margin: 0;
  line-height: 1.25;
  color: #3f4a4f;
  overflow-wrap: anywhere;
}

.zone-picker-zone-name {
  color: #7a888c;
  white-space: nowrap;
}

.zone-picker-price {
  line-height: 1.25;
  color: #3f4a4f;
  white-space: nowrap;
}

.zone-picker-divider {
  height: 1px;
  background: #c6d0d3;
}

.zone-picker-form {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.zone-picker-label {
  color: #3f4a4f;
}

.zone-picker-input {
  width: 100%;
  height: 42px;
  border-radius: 8px;
  border: 1px solid #c8d1d4;
  background: #e6eaec;
  color: #4d5c60;
  font-size: 14px;
  padding: 0 12px;
  box-sizing: border-box;
}

.zone-picker-input:focus {
  outline: 2px solid #005f6f;
  outline-offset: 1px;
}

.zone-picker-dates-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 10px;
}

.zone-picker-date-wrap {
  position: relative;
}

.zone-picker-date {
  padding-right: 34px;
}

.zone-picker-date::-webkit-calendar-picker-indicator {
  opacity: 0;
  width: 34px;
  cursor: pointer;
}

.zone-picker-date-icon {
  position: absolute;
  right: 9px;
  top: 50%;
  transform: translateY(-50%);
  color: #4d5c60;
  pointer-events: none;
}

.zone-picker-note {
  margin: 10px 0 0;
  color: #73858a;
  line-height: 1.35;
}

.zone-picker-feedback {
  margin: 8px 0 0;
  font-size: 13px;
  line-height: 1.35;
  font-weight: 600;
}

.zone-picker-feedback.info {
  color: #0b4b58;
}

.zone-picker-feedback.success {
  color: #027a48;
}

.zone-picker-feedback.error {
  color: #b42318;
}

.zone-payment-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.zone-payment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 16px;
}

.zone-payment-title {
  margin: 0;
  font-size: 28px;
  color: #242b2c;
  font-weight: 700;
}

.zone-payment-price {
  font-size: 28px;
  font-weight: 700;
  color: #242b2c;
}

.zone-payment-amount {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px;
  background: #f3f4f5;
  border-radius: 12px;
}

.zone-payment-amount-label {
  font-size: 14px;
  font-weight: 600;
  color: #414d4f;
}

.zone-payment-amount-value {
  font-size: 20px;
  font-weight: 700;
  color: #242b2c;
}

.zone-payment-divider {
  height: 1px;
  background: #c6d0d3;
}


.zone-payment-demo-notice {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  padding: 12px 14px;
  background: #fff7ed;
  border: 1px solid #fed7aa;
  border-radius: 10px;
  font-size: 13px;
  color: #92400e;
  line-height: 1.45;
}

.zone-payment-demo-notice svg {
  flex-shrink: 0;
  margin-top: 1px;
}

.zone-payment-mock-card {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 20px 24px;
  background: linear-gradient(135deg, #1e3a5f 0%, #005f6f 100%);
  border-radius: 14px;
  color: #ffffff;
  font-family: monospace;
  user-select: none;
  opacity: 0.85;
}

.zone-payment-mock-card-number {
  font-size: 18px;
  letter-spacing: 2px;
}

.zone-payment-mock-card-row {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
  opacity: 0.85;
}



.zone-checkout-summary {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.zone-checkout-title {
  margin: 0;
  font-size: 20px;
  color: #242b2c;
  font-weight: 700;
}

.zone-checkout-table {
  border-top: 1px solid #c6d0d3;
  border-bottom: 1px solid #c6d0d3;
  padding: 12px 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.zone-checkout-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  align-items: center;
}

.zone-checkout-label {
  font-size: 13px;
  font-weight: 600;
  color: #73858a;
}

.zone-checkout-value {
  font-size: 13px;
  font-weight: 700;
  color: #3f4a4f;
  text-align: right;
}

.zone-checkout-total {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 20px;
  line-height: 1.2;
  font-weight: 700;
  color: #3f4a4f;
}

.zone-success-page {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.zone-success-header {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 16px 0;
}

.zone-success-icon {
  color: #059669;
  animation: scaleIn 0.4s ease-out;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}

.zone-success-title {
  margin: 0;
  font-size: 24px;
  font-weight: 700;
  color: #242b2c;
  text-align: center;
}

.zone-success-order-id {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 12px;
  background: #f0fdf4;
  border-radius: 12px;
}

.zone-success-label {
  font-size: 12px;
  font-weight: 600;
  color: #73858a;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.zone-success-value {
  font-size: 18px;
  font-weight: 700;
  color: #059669;
  font-family: 'Courier New', monospace;
}

.zone-success-divider {
  height: 1px;
  background: #e5e7eb;
}

.zone-success-details {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.zone-success-row {
  display: flex;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 0;
}

.zone-success-row-total {
  padding: 12px 0;
  border-top: 1px solid #e5e7eb;
  font-size: 16px;
  font-weight: 700;
}

.zone-success-detail-label {
  font-size: 13px;
  font-weight: 600;
  color: #73858a;
}

.zone-success-detail-value {
  font-size: 13px;
  font-weight: 700;
  color: #242b2c;
  text-align: right;
}

.zone-success-note {
  margin: 8px 0 0;
  font-size: 12px;
  color: #73858a;
  line-height: 1.4;
  text-align: center;
  font-style: italic;
}

.zone-picker-actions {
  border-top: 1px solid #cdd6d9;
  background: #f3f4f5;
  padding: clamp(10px, 1.8vw, 14px) clamp(12px, 2.2vw, 18px);
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

.zone-picker-back-btn,
.zone-picker-checkout-btn {
  width: 100%;
}

.zone-picker-back-btn {
  justify-self: stretch;
}

.zone-picker-checkout-btn {
  justify-self: stretch;
}

.zone-picker-checkout-btn:disabled {
  opacity: 0.55;
  cursor: not-allowed;
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

  .beaches-view.checkout-mode .zone-picker-menu {
    border-radius: 0 20px 20px 0;
    background: transparent;
  }

  .beaches-view.checkout-mode .zone-picker-hero {
    border-radius: 0 20px 0 0;
    overflow: hidden;
  }

  .beaches-view.checkout-mode .zone-picker-content {
    background: #fafafc;
  }

  .beaches-view.checkout-mode .zone-picker-actions {
    background: #f3f4f5;
    border-radius: 0 0 20px 20px;
  }

  .back-section {
    padding: 0 16px;
  }

  .search-box {
    margin: 0 16px 16px 16px;
  }

  .beaches-header {
    padding: 0 16px;
  }

  .beaches-list {
    padding: 0 16px;
  }

  .zone-picker-content {
    padding: 12px;
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

  .beaches-view.checkout-mode .zone-picker-menu,
  .beaches-view.checkout-mode .zone-picker-hero,
  .beaches-view.checkout-mode .zone-picker-hero-image {
    border-radius: 0;
  }
}

@media (max-width: 640px) {
  .zone-picker-hero {
    height: 200px;
  }

  .zone-picker-dates-grid {
    grid-template-columns: 1fr;
  }

  .zone-picker-actions {
    grid-template-columns: 1fr 1fr;
  }
}
</style>
