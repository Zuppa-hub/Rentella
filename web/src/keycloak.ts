import Keycloak from 'keycloak-js'

const keycloak = new Keycloak({
  url: import.meta.env.VITE_KEYCLOAK_URL,
  realm: import.meta.env.VITE_KEYCLOAK_REALM,
  clientId: import.meta.env.VITE_KEYCLOAK_CLIENT_ID,
})

const getRedirectUri = (): string =>
  import.meta.env.VITE_KEYCLOAK_REDIRECT_URI || `${window.location.origin}/`

export const initKeycloak = async (): Promise<boolean> => {
  return keycloak.init({
    onLoad: 'check-sso',
    pkceMethod: 'S256',
    checkLoginIframe: false,
    responseMode: 'query',
    redirectUri: getRedirectUri(),
  })
}

export const login = (): Promise<void> => keycloak.login({ redirectUri: getRedirectUri() })

export const logout = (): Promise<void> => keycloak.logout({ redirectUri: getRedirectUri() })

export const isAuthenticated = (): boolean => !!keycloak.authenticated

export const getToken = (): string | undefined => keycloak.token

export const getUser = (): Record<string, unknown> | undefined =>
  keycloak.tokenParsed as Record<string, unknown> | undefined

export const updateToken = (minValiditySeconds = 60): Promise<boolean> =>
  keycloak.updateToken(minValiditySeconds)

export default keycloak
