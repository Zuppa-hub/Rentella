/**
 * Structured Logging Utility
 * Provides consistent logging across the application
 */

import { LOG_LEVEL, DEBUG_MODE } from '../constants/index'

type LogLevel = 'debug' | 'info' | 'warn' | 'error'

interface LogEntry {
  timestamp: string
  level: LogLevel
  module: string
  message: string
  data?: unknown
}

// Store logs for debugging/analytics
const logBuffer: LogEntry[] = []
const MAX_LOG_BUFFER = 100

class Logger {
  private module: string

  constructor(module: string) {
    this.module = module
  }

  private shouldLog(level: LogLevel): boolean {
    const levels: Record<LogLevel, number> = {
      debug: 0,
      info: 1,
      warn: 2,
      error: 3,
    }
    return levels[level] >= levels[LOG_LEVEL as LogLevel]
  }

  private addToBuffer(entry: LogEntry) {
    logBuffer.push(entry)
    if (logBuffer.length > MAX_LOG_BUFFER) {
      logBuffer.shift() // Remove oldest
    }
  }

  private formatMessage(message: string, data?: unknown): string {
    if (data) {
      return `[${this.module}] ${message}: ${JSON.stringify(data)}`
    }
    return `[${this.module}] ${message}`
  }

  debug(message: string, data?: unknown) {
    if (this.shouldLog('debug')) {
      const entry: LogEntry = {
        timestamp: new Date().toISOString(),
        level: 'debug',
        module: this.module,
        message,
        data,
      }
      this.addToBuffer(entry)
      console.debug(this.formatMessage(message, data), data)
    }
  }

  info(message: string, data?: unknown) {
    if (this.shouldLog('info')) {
      const entry: LogEntry = {
        timestamp: new Date().toISOString(),
        level: 'info',
        module: this.module,
        message,
        data,
      }
      this.addToBuffer(entry)
      console.info(this.formatMessage(message, data), data)
    }
  }

  warn(message: string, data?: unknown) {
    if (this.shouldLog('warn')) {
      const entry: LogEntry = {
        timestamp: new Date().toISOString(),
        level: 'warn',
        module: this.module,
        message,
        data,
      }
      this.addToBuffer(entry)
      console.warn(this.formatMessage(message, data), data)
    }
  }

  error(message: string, error?: Error | unknown) {
    const entry: LogEntry = {
      timestamp: new Date().toISOString(),
      level: 'error',
      module: this.module,
      message,
      data: error instanceof Error ? error.message : error,
    }
    this.addToBuffer(entry)
    console.error(this.formatMessage(message, error), error)
  }

  static getLogs(): LogEntry[] {
    return [...logBuffer]
  }

  static clearLogs() {
    logBuffer.length = 0
  }

  static exportLogs(): string {
    return JSON.stringify(logBuffer, null, 2)
  }
}

export const createLogger = (module: string) => new Logger(module)
