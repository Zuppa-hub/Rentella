export function getTodayIsoDate(): string {
  return new Date().toISOString().slice(0, 10)
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
