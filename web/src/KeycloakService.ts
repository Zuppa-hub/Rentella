import Keycloak from "keycloak-js";

/**
 * Type definition for authentication callback
 */
type AuthCallback = (authenticated: boolean) => void;

/**
 * Keycloak singleton instance
 */
const keycloakInstance = new Keycloak();

/**
 * Handle token expiration and refresh
 */
const UpdateToken = keycloakInstance.onTokenExpired = function () {
  keycloakInstance
    .updateToken(3000)
    .catch((e) => {
      console.error("Keycloak token update failed", e);
    });
};

/**
 * Initialize Keycloak authentication
 */
const Login = (onAuthenticatedCallback: AuthCallback): void => {
  keycloakInstance
    .init({ onLoad: "login-required", responseMode: "query" })
    .then(function (authenticated) {
      if (authenticated) {
        onAuthenticatedCallback(true);
      } else {
        console.warn("User not authenticated");
        onAuthenticatedCallback(false);
      }
    })
    .catch((e) => {
      console.error("Keycloak initialization error", e);
      onAuthenticatedCallback(false);
    });
};

/**
 * Get authenticated username
 */
const GetUserName = (): string | undefined =>
  keycloakInstance?.tokenParsed?.preferred_username;

/**
 * Get authenticated user ID
 */
const GetUid = (): string | undefined =>
  keycloakInstance?.tokenParsed?.sub;

/**
 * Get access token for API calls
 */
const GetAccesToken = (): string | undefined => {
  UpdateToken();
  return keycloakInstance?.token;
};

/**
 * Check if user is authenticated
 */
const IsAuthenticated = (): boolean => {
  return keycloakInstance?.authenticated ?? false;
};

/**
 * Logout user
 */
const CallLogOut = () => {
  keycloakInstance.logout();
};

/**
 * Get user roles
 */
const GetUserRoles = (): string[] => {
  try {
    if (!keycloakInstance?.resourceAccess?.["api"]) {
      return [];
    }
    return keycloakInstance.resourceAccess["api"].roles || [];
  } catch (error) {
    console.error("Error fetching user roles", error);
    return [];
  }
};

/**
 * Keycloak Service - Manages authentication and token management
 */
const KeyCloakService = {
  GetUid,
  CallLogin: Login,
  GetUserName,
  GetAccesToken,
  IsAuthenticated,
  CallLogOut,
  GetUserRoles,
  UpdateToken,
};

export default KeyCloakService;