export function getTodayIsoDate(): string {
  const now = new Date()
  const year = now.getFullYear()
  const month = String(now.getMonth() + 1).padStart(2, '0')
  const day = String(now.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

export function getCompactTodayDate(): string {
  return getTodayIsoDate().replace(/-/g, '')
}

export function formatIsoDateToIt(value: string): string {
  if (!value) return value
  const parts = value.split('-')
  if (parts.length !== 3) return value
  const [year, month, day] = parts
  return `${day}.${month}.${year}`
}
