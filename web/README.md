# Frontend

The UI. Built with Vue 3 + TypeScript + Vite.

## Quick start

```bash
cd web
npm install
npm run dev
```

Runs on `http://localhost:5173`

## Architecture

### Project structure

```
src/
├── pages/              # Route-level components
│   ├── Home.vue
│   ├── BeachDetail.vue
│   └── Dashboard.vue
├── components/         # Reusable components
│   ├── BeachCard.vue
│   ├── SearchBar.vue
│   └── OrderForm.vue
├── stores/             # Pinia state management
│   ├── useBeachStore.ts
│   ├── useAuthStore.ts
│   └── useOrderStore.ts
├── services/
│   ├── api.ts          # API client
│   └── index.ts        # All imports
├── router/
│   └── index.ts        # Vue Router setup
├── types/
│   ├── Beach.ts
│   ├── User.ts
│   └── Order.ts
├── assets/             # Images, styles
├── App.vue             # Root component
└── main.ts             # Entry point
```

### How it works

1. **User logs in** → Keycloak redirects
2. **Token stored** → In localStorage
3. **API requests** → Token sent as Bearer header
4. **Data fetched** → Pinia stores manage state
5. **UI updates** → Vue reactivity handles it

### Key files

- `App.vue` - Root layout
- `main.ts` - App initialization
- `services/api.ts` - All API calls
- `stores/useAuthStore.ts` - Auth state
- `router/index.ts` - Route definitions

## Development

### Commands

```bash
# Dev server with hot reload
npm run dev

# Build for production
npm run build

# Preview production build
npm run preview

# Check code style
npm run lint

# Fix code style issues
npm run format
```

### Components

Create new component:
```bash
# Create file
echo '<template><div></div></template>' > src/components/MyComponent.vue
```

Or manually in `src/components/MyComponent.vue`:

```vue
<template>
  <div class="my-component">
    <h1>{{ title }}</h1>
    <button @click="handleClick">Click me</button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Props {
  title: string;
}

withDefaults(defineProps<Props>(), {
  title: 'Default',
});

const count = ref(0);

function handleClick() {
  count.value++;
}
</script>

<style scoped>
.my-component {
  padding: 1rem;
}
</style>
```

### Stores (Pinia)

Define state in `src/stores/`:

```typescript
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

export const useBeachStore = defineStore('beach', () => {
  const beaches = ref([]);
  const loading = ref(false);

  const total = computed(() => beaches.value.length);

  async function fetchBeaches() {
    loading.value = true;
    try {
      const response = await api.get('/beaches');
      beaches.value = response.data;
    } finally {
      loading.value = false;
    }
  }

  return {
    beaches,
    loading,
    total,
    fetchBeaches,
  };
});
```

Use in components:
```vue
<script setup lang="ts">
import { useBeachStore } from '@/stores/useBeachStore';

const store = useBeachStore();

onMounted(() => {
  store.fetchBeaches();
});
</script>

<template>
  <div v-if="store.loading">Loading...</div>
  <div v-else>
    <p>Found {{ store.total }} beaches</p>
  </div>
</template>
```

### API calls

All API functions in `src/services/api.ts`:

```typescript
import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:9000/api',
});

// Add token to requests
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export const beachAPI = {
  getAll: () => api.get('/beaches'),
  getOne: (id: number) => api.get(`/beaches/${id}`),
  create: (data: any) => api.post('/beaches', data),
  update: (id: number, data: any) => api.put(`/beaches/${id}`, data),
  delete: (id: number) => api.delete(`/beaches/${id}`),
};
```

### Routing

Routes in `src/router/index.ts`:

```typescript
import { createRouter, createWebHistory } from 'vue-router';
import Home from '@/pages/Home.vue';
import BeachDetail from '@/pages/BeachDetail.vue';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/beaches/:id',
    name: 'BeachDetail',
    component: BeachDetail,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
```

## Styling

Using Tailwind CSS. Add utility classes directly:

```vue
<template>
  <div class="flex gap-4 p-6">
    <button class="bg-blue-500 text-white px-4 py-2 rounded">
      Click me
    </button>
  </div>
</template>
```

Easy customization in `tailwind.config.js`

## Authentication

### Login flow

1. Check localStorage for token
2. If no token, redirect to Keycloak login
3. After login, receive token
4. Store in localStorage
5. Send with every API request

In `useAuthStore.ts`:

```typescript
export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token'));

  async function login(email: string, password: string) {
    // Call Keycloak or your auth endpoint
    const response = await api.post('/login', { email, password });
    token.value = response.data.token;
    localStorage.setItem('token', token.value);
  }

  function logout() {
    token.value = null;
    localStorage.removeItem('token');
  }

  return {
    user,
    token,
    login,
    logout,
  };
});
```

## TypeScript

Everything typed. Define interfaces in `src/types/`:

```typescript
// src/types/Beach.ts
export interface Beach {
  id: number;
  name: string;
  location: string;
  owner_id: number;
  created_at: string;
}

export interface BeachZone {
  id: number;
  beach_id: number;
  name: string;
}
```

Use in components:
```typescript
import type { Beach } from '@/types/Beach';

const beach: Ref<Beach | null> = ref(null);
```

## Performance tips

- Use `v-if` instead of `v-show` for hidden content
- Lazy load routes
- Memoize expensive computations
- Minimize re-renders

## Testing

(To be implemented - Phase 2)

## Environment variables

Create `.env` file:
```
VITE_API_URL=http://localhost:9000/api
VITE_KEYCLOAK_URL=http://localhost:8080
```

Access in code:
```typescript
const apiUrl = import.meta.env.VITE_API_URL;
```

## Build

```bash
npm run build
```

Outputs to `dist/` folder. Ready for deployment.

## That's it

Read the components. They're simple. Questions? Check the store and service files.
