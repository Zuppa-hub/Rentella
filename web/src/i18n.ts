import { createI18n } from 'vue-i18n'

export const messages = {
  en: {
    common: {
      close: 'Close',
      cancel: 'Cancel',
      continue: 'Continue',
    },
    welcome: {
      title: 'Rentella',
      subtitle: 'Discover the best beaches near you',
      feature1: 'Find nearby beaches',
      feature2: 'Check real-time prices',
      feature3: 'See beach photos',
      login: 'Login with Keycloak',
    },
    desktop: {
      brand: {
        alt: 'Rentella',
      },
      nav: {
        home: 'Home',
        active: 'Active',
        history: 'History',
        settings: 'Settings',
      },
      search: {
        placeholder: 'Search for a location',
      },
      locations: {
        title: 'Available locations',
        count: 'Showing {count} locations',
      },
      map: {
        myLocation: 'Your location',
        loading: 'Loading position...',
      },
      location: {
        prices: 'Price Range',
        beaches: 'Available Beaches',
        distance: 'Distance',
        beachCount: 'Beach Count',
        noBeaches: 'No beaches available',
        exploreBeaches: 'Explore Beaches',
      },
      common: {
        close: 'Close',
        select: 'Select',
        cancel: 'Cancel',
        continue: 'Continue',
      },
    },
  },
  it: {
    common: {
      close: 'Chiudi',
      cancel: 'Annulla',
      continue: 'Continua',
    },
    welcome: {
      title: 'Rentella',
      subtitle: 'Scopri le migliori spiagge vicino a te',
      feature1: 'Trova spiagge vicine',
      feature2: 'Controlla i prezzi in tempo reale',
      feature3: 'Vedi foto delle spiagge',
      login: 'Accedi con Keycloak',
    },
    desktop: {
      brand: {
        alt: 'Rentella',
      },
      nav: {
        home: 'Home',
        active: 'Attive',
        history: 'Storico',
        settings: 'Impostazioni',
      },
      search: {
        placeholder: 'Cerca una location',
      },
      locations: {
        title: 'Location disponibili',
        count: 'Mostrando {count} location',
      },
      map: {
        myLocation: 'La tua posizione',
        loading: 'Caricamento posizione...',
      },
      location: {
        prices: 'Fascia Prezzo',
        beaches: 'Spiagge Disponibili',
        distance: 'Distanza',
        beachCount: 'Numero Spiagge',
        noBeaches: 'Nessuna spiaggia disponibile',
        exploreBeaches: 'Esplora Spiagge',
      },
      common: {
        close: 'Chiudi',
        select: 'Seleziona',
        cancel: 'Annulla',
        continue: 'Continua',
      },
    },
  },
} as const

export const i18n = createI18n({
  legacy: false,
  locale: 'it',
  fallbackLocale: 'en',
  messages,
})
