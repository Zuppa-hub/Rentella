import Keycloak from "keycloak-js";

/* The line `const keycloakInstance = new Keycloak();` is creating a new instance of the Keycloak
class. This instance will be used to interact with the Keycloak authentication server and perform
authentication-related operations such as initializing the Keycloak instance, updating tokens,
logging in, logging out, and retrieving user information. */
const keycloakInstance = new Keycloak();

const UpdateToken = keycloakInstance.onTokenExpired = function () {
  keycloakInstance
    .updateToken(3000)
    .catch((e) => {
      console.error("Keycloak update exception", e);
      // Puoi gestire l'errore in base alle tue esigenze
    });
};

interface CallbackOneParam<T1 = void, T2 = void> {
  (param1: T1): T2;
}
/**
 * Initializes Keycloak instance and calls the provided callback function if successfully authenticated.
 *
 * @param onAuthenticatedCallback
 */
const Login = (onAuthenticatedCallback: CallbackOneParam): void => {
  keycloakInstance
    .init({ onLoad: "login-required", responseMode: "query" })
    .then(function (authenticated) {
      authenticated ? onAuthenticatedCallback() : alert("non authenticated");
    })
    .catch((e) => {
      console.dir(e);
      console.log(`keycloak init exception: ${e}`);
    });
};

const UserName = (): string | undefined =>
  keycloakInstance?.tokenParsed?.preferred_username;
const Uid = (): string | undefined =>
  keycloakInstance?.tokenParsed?.sub;

const Token = (): string | undefined => {
  UpdateToken();
  return keycloakInstance?.token;
}

const LogOut = () => keycloakInstance.logout();

const UserRoles = (): string[] | undefined => {
  if (keycloakInstance.resourceAccess === undefined) return undefined;
  return keycloakInstance.resourceAccess["api"].roles;
};

const KeyCloakService = {
  GetUid: Uid,
  CallLogin: Login,
  GetUserName: UserName,
  GetAccesToken: Token,
  CallLogOut: LogOut,
  GetUserRoles: UserRoles,
  UpdateToken: UpdateToken
};

export default KeyCloakService;