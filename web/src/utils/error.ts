/**
 * Error Handling Utilities
 * Centralized error management for production
 */

import { createLogger } from './logger'
import { ERROR_MESSAGES } from '../constants/index'

const logger = createLogger('ErrorHandler')

export interface ApiErrorResponse {
  status: number
  message: string
  code: string
  details?: unknown
}

export class ApiError extends Error {
  status: number
  code: string
  details?: unknown

  constructor(message: string, status: number, code: string, details?: unknown) {
    super(message)
    this.name = 'ApiError'
    this.status = status
    this.code = code
    this.details = details
  }
}

export class ErrorHandler {
  /**
   * Parse API errors into normalized format
   */
  static parseApiError(error: unknown): ApiError {
    if (error instanceof ApiError) {
      return error
    }

    if (error instanceof TypeError && error.message.includes('Failed to fetch')) {
      logger.error('Network error detected', error)
      return new ApiError(
        ERROR_MESSAGES.API_NETWORK_ERROR,
        0,
        'NETWORK_ERROR',
        error
      )
    }

    if (error instanceof Error) {
      logger.error('Unexpected error', error)
      return new ApiError(
        error.message,
        500,
        'UNKNOWN_ERROR',
        error
      )
    }

    logger.error('Unknown error type', error)
    return new ApiError(
      ERROR_MESSAGES.API_SERVER_ERROR,
      500,
      'UNKNOWN_ERROR',
      error
    )
  }

  /**
   * Check if error is a network error
   */
  static isNetworkError(error: unknown): boolean {
    if (error instanceof ApiError) {
      return error.code === 'NETWORK_ERROR'
    }
    return error instanceof TypeError && 'Failed to fetch' in String(error)
  }

  /**
   * Check if error is an authentication error
   */
  static isAuthError(error: unknown): boolean {
    if (error instanceof ApiError) {
      return error.status === 401 || error.code === 'UNAUTHORIZED'
    }
    return false
  }

  /**
   * Check if error is a server error
   */
  static isServerError(error: unknown): boolean {
    if (error instanceof ApiError) {
      return error.status >= 500
    }
    return false
  }

  /**
   * Check if error is a client error
   */
  static isClientError(error: unknown): boolean {
    if (error instanceof ApiError) {
      return error.status >= 400 && error.status < 500
    }
    return false
  }

  /**
   * Get user-friendly error message
   */
  static getUserMessage(error: unknown): string {
    const apiError = this.parseApiError(error)

    const messageMap: Record<string, string> = {
      NETWORK_ERROR: ERROR_MESSAGES.API_NETWORK_ERROR,
      UNAUTHORIZED: ERROR_MESSAGES.API_UNAUTHORIZED,
      NOT_FOUND: 'Resource not found',
      SERVER_ERROR: ERROR_MESSAGES.API_SERVER_ERROR,
      UNKNOWN_ERROR: 'An unexpected error occurred. Please try again.',
    }

    return messageMap[apiError.code] || apiError.message
  }

  /**
   * Log error with context
   */
  static logError(context: string, error: unknown) {
    const apiError = this.parseApiError(error)
    logger.error(`[${context}] ${apiError.message}`, {
      status: apiError.status,
      code: apiError.code,
      details: apiError.details,
    })
  }
}
