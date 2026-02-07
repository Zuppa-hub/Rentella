/**
 * @deprecated Use services/api.ts instead. This file is kept for backwards compatibility.
 * All new code should use the new API client with axios interceptors.
 */
import KeyCloakService from "./KeycloakService";

/**
 * The `apiHelper` function is an async function that makes an API request with the specified URL and
 * method, including an authorization token in the headers, and returns the response as JSON if it is
 * successful, otherwise it throws an error.
 * 
 * @deprecated Use services/api.ts - apiGet, apiPost, etc. instead
 * 
 * @param {string} url - The `url` parameter is a string that represents the URL of the API endpoint
 * @param {string} method - The HTTP method to be used
 * @returns a Promise that resolves to any type of value
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

/**
 * The `Geolocate` method is using the browser's geolocation API to retrieve the user's current
 * latitude and longitude coordinates.
 * 
 * @returns Promise with latitude and longitude
 */
export async function Geolocate(): Promise<{ latitude: number; longitude: number }> {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) {
            reject(new Error('Geolocation is not supported by this browser'));
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const coordinates = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                };
                resolve(coordinates);
            },
            (error) => {
                let errorMessage = 'Unknown geolocation error';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage = 'Geolocation permission denied';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'Geolocation position unavailable';
                        break;
                    case error.TIMEOUT:
                        errorMessage = 'Geolocation request timeout';
                        break;
                }
                reject(new Error(errorMessage));
            },
            {
                timeout: 10000,
                enableHighAccuracy: true,
            }
        );
    });
}
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


