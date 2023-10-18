// apiService.ts
import KeyCloakService from "./KeycloakService";
/**
 * The function fetches order data from a specified URL using an access token for authorization.
 * @param {string} url - The `url` parameter is a string that represents the URL from which to fetch
 * the order data.
 * @returns json 
 */
export async function fetchData(url: string): Promise<any> {
    const token = KeyCloakService.GetAccesToken();
    const headers: HeadersInit = {
        'Authorization': `Bearer ${token}`,
    };
    try {
        const response = await fetch(`${url}`, { headers });
        return response.json();
    } catch (error) {
        console.error(error);
        throw error;
    }
}
