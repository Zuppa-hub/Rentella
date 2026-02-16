/**
 * Shared utility functions for components
 */

/**
 * Normalize allowed animals value to boolean
 */
export function isAnimalsAllowed(value: any): boolean {
  if (typeof value === 'boolean') return value
  if (typeof value === 'number') return value === 1
  if (typeof value === 'string') {
    const normalized = value.trim().toLowerCase()
    return ['yes', 'si', 'true', '1'].includes(normalized)
  }
  return false
}

/**
 * Convert any value to number safely
 */
export function toNumber(value: unknown): number | null {
  if (typeof value === 'number' && Number.isFinite(value)) return value
  const parsed = Number.parseFloat(String(value))
  return Number.isFinite(parsed) ? parsed : null
}

/**
 * Parse beach type ID safely
 */
export function parseBeachTypeId(rawId: any): number | null {
  const parsed = typeof rawId === 'number' ? rawId : Number.parseInt(String(rawId), 10)
  return Number.isFinite(parsed) ? parsed : null
}

/**
 * Search filter function
 */
export function createSearchFilter(searchTerm: string) {
  if (!searchTerm) return () => true
  const query = searchTerm.toLowerCase()
  return (item: { name: string }) => item.name.toLowerCase().includes(query)
}
