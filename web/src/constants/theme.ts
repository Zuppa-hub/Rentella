/**
 * Design system colors and tokens
 */

export const colors = {
  // Primary palette
  primary: '#005f6f',
  primaryDark: '#003B45',
  primaryLight: '#0b5f6f',

  // Neutral palette
  neutral: {
    50: '#fafafc',
    100: '#f3f4f6',
    200: '#f2f4f6',
    300: '#e5e7eb',
    400: '#d2d8e0',
    500: '#9ca3af',
    600: '#78898c',
    700: '#6b7280',
    800: '#414d4f',
    900: '#242b2c',
    950: '#0b0b0b',
  },

  // Background
  background: '#ffffff',
  backgroundAlt: '#fafafc',

  // Text
  text: '#414d4f',
  textSecondary: '#78898c',
  textTertiary: '#9ca3af',

  // Border
  border: '#dae2e3',

  // UI elements
  badge: '#ffffff',

  // Shadows
  shadow: 'rgba(136, 136, 153, 0.08)',
  shadowDark: 'rgba(15, 23, 42, 0.08)',
} as const

export const spacing = {
  xs: '4px',
  sm: '8px',
  md: '12px',
  lg: '16px',
  xl: '20px',
  '2xl': '32px',
} as const

export const borderRadius = {
  sm: '8px',
  md: '12px',
  lg: '16px',
  xl: '32px',
} as const

export const fontSize = {
  xs: '12px',
  sm: '13px',
  base: '14px',
  lg: '16px',
  xl: '20px',
} as const

export const fontWeight = {
  normal: 400,
  medium: 500,
  semibold: 600,
  bold: 700,
} as const
