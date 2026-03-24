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

const DAY_IN_MS = 1000 * 60 * 60 * 24

const toUtcDateOnly = (value: string): Date | null => {
  const match = value.match(/^(\d{4})-(\d{2})-(\d{2})/)
  if (!match) return null

  const [, yearStr, monthStr, dayStr] = match
  const year = Number(yearStr)
  const month = Number(monthStr)
  const day = Number(dayStr)

  if (!Number.isFinite(year) || !Number.isFinite(month) || !Number.isFinite(day)) {
    return null
  }

  return new Date(Date.UTC(year, month - 1, day))
}

export function calculateBookingDays(startDate: string, endDate: string): number {
  const start = toUtcDateOnly(startDate)
  const end = toUtcDateOnly(endDate)

  if (!start || !end) return 1

  const diffMs = end.getTime() - start.getTime()
  if (diffMs <= 0) return 1

  return Math.max(1, Math.ceil(diffMs / DAY_IN_MS))
}
