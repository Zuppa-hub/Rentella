<template>
  <div class="search-box">
    <svg viewBox="0 0 24 24" class="search-icon" aria-hidden="true">
      <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" fill="none" />
      <path d="M20 20l-4-4" stroke="currentColor" stroke-width="2" fill="none" />
    </svg>
    <input
      :value="modelValue"
      type="text"
      :placeholder="placeholder"
      class="search-input"
      @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
    />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: string
  placeholder?: string
}>()

defineEmits<{
  'update:modelValue': [value: string]
}>()
</script>

<style scoped>
.search-box {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0 0 16px;
  padding: 10px;
  background: #f2f4f6;
  border: 1px solid #dae2e3;
  border-radius: 8px;
  box-shadow: 0px 2px 2px rgba(136, 136, 153, 0.08);
}

.search-icon {
  width: 20px;
  height: 20px;
  color: #78898c;
  flex-shrink: 0;
}

.search-input {
  flex: 1;
  border: none;
  background: transparent;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  color: #414d4f;
  outline: none;
}

.search-input::placeholder {
  color: #78898c;
}

@media (min-width: 1024px) {
  .search-box {
    margin: 0 16px 16px 16px;
  }
}
</style>
