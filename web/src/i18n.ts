import { createI18n } from 'vue-i18n'

export const messages = {
  en: {
    common: {
      close: 'Close',
      cancel: 'Cancel',
      continue: 'Continue',
      back: 'Back',
      beaches: 'Beaches',
    },
    setLocation: {
      title: 'Location Sharing',
      description: 'In order to provide you with the best experience and to ensure accurate results, we need to know your location. You can use your Current Location or set it Manually',
      searchLabel: 'Set your Location',
      searchPlaceholder: 'Choose your Location...',
      back: 'Back',
      done: 'Done',
      saving: 'Saving...',
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
      beach: {
        type: 'Type',
        typeLabel: 'Beach Type',
        animals: 'Allowed animals',
        dogsAllowed: 'Dogs Allowed',
        priceRange: 'Price range',
        typeUnknown: 'Unknown type',
        loadingZones: 'Loading zones...',
        noZones: 'No zones available',
        zonesLoadError: 'Failed to load zones',
        zoneNumber: 'Zone {number}',
        available: 'available',
        yes: 'Yes',
        no: 'No',
      },
      common: {
        close: 'Close',
        select: 'Select',
        selectBeach: 'Select Beach',
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
      back: 'Indietro',
      beaches: 'Spiagge',
    },
    setLocation: {
      title: 'Condivisione Posizione',
      description: 'Per offrirti la migliore esperienza e garantire risultati accurati, abbiamo bisogno di conoscere la tua posizione. Puoi usare la Posizione Attuale o impostarla Manualmente',
      searchLabel: 'Imposta la tua Posizione',
      searchPlaceholder: 'Scegli la tua Posizione...',
      back: 'Indietro',
      done: 'Fatto',
      saving: 'Salvataggio...',
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
      beach: {
        type: 'Tipo',
        typeLabel: 'Tipo Spiaggia',
        animals: 'Animali ammessi',
        dogsAllowed: 'Cani ammessi',
        priceRange: 'Fascia prezzo',
        typeUnknown: 'Tipo sconosciuto',
        loadingZones: 'Caricamento zone...',
        noZones: 'Nessuna zona disponibile',
        zonesLoadError: 'Errore nel caricamento delle zone',
        zoneNumber: 'Zona {number}',
        available: 'disponibili',
        yes: 'Sì',
        no: 'No',
      },
      common: {
        close: 'Chiudi',
        select: 'Seleziona',
        selectBeach: 'Seleziona Spiaggia',
        cancel: 'Annulla',
        continue: 'Continua',
      },
    },
  },
} as const

export const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'it',
  messages,
})
