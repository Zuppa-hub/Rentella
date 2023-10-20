// apiService.ts
import KeyCloakService from "./KeycloakService";
/**
 * The `apiHelper` function is an async function that makes an API request with the specified URL and
 * method, including an authorization token in the headers, and returns the response as JSON if it is
 * successful, otherwise it throws an error.
 * @param {string} url - The `url` parameter is a string that represents the URL of the API endpoint
 * that you want to make a request to. It should include the protocol (e.g., "http://" or "https://")
 * and the domain name or IP address.
 * @param {string} method - The `method` parameter in the `apiHelper` function is a string that
 * represents the HTTP method to be used in the API request. It can be one of the following values:
 * @returns a Promise that resolves to any type of value.
 */
export async function apiHelper(url: string, method: string): Promise<any> {
    const token = KeyCloakService.GetAccesToken();
    const headers: HeadersInit = {
        'Authorization': `Bearer ${token}`,
    };
    try {
        const response = await fetch(url, {
            method: method,
            headers,
        });
        if (response.ok) {
            return response.json();
        } else {
            throw new Error(`Error ${method}: ${response.status} - ${response.statusText}`);
        }
    } catch (error) {
        console.error(error);
        throw error;
    }
}
