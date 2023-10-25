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
// The `Geolocate` method is using the browser's geolocation API to retrieve the user's current
// latitude and longitude coordinates. It checks if the `navigator.geolocation` object is
// available and then calls the `getCurrentPosition` method to get the user's position. Once
// the position is obtained, the latitude and longitude values are stored in the component's
// `myLatitude` and `myLongitude` data properties.
export async function Geolocate(): Promise<{ latitude: number; longitude: number }> {
    return new Promise((resolve, reject) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const coordinates = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                    };
                    resolve(coordinates);
                },
                (error) => {
                    reject(error);
                }
            );
        } else {
            reject(new Error("Geolocation is not available in this browser"));
        }
    });
}
export function findMostFrequentElement(arr: any[]): any | null {
    if (arr.length === 0) {
        return null; // Return null if the array is empty
    }

    const elementCountMap = new Map<any, number>();

    // Count how many times each element appears in the array
    for (const element of arr) {
        if (elementCountMap.has(element)) {
            elementCountMap.set(element, elementCountMap.get(element)! + 1);
        } else {
            elementCountMap.set(element, 1);
        }
    }

    let mostFrequentElement = arr[0]; // Initialize with the first element
    let maxCount = 1; // Initialize with the frequency of the first element

    // Find the most frequent element
    for (const [element, count] of elementCountMap) {
        if (count > maxCount) {
            mostFrequentElement = element;
            maxCount = count;
        }
    }
    console.log(mostFrequentElement);
    return mostFrequentElement;
}


