/**
 * Generate a URL for a named route
 * 
 * @param {string} name - The name of the route
 * @param {Object} params - The parameters for the route
 * @returns {string} The generated URL
 */
window.route = function(name, params = {}) {
    if (!window.routes || !window.routes[name]) {
        console.error(`Route "${name}" not found.`);
        return '#';
    }

    let url = window.routes[name];

    // Replace named parameters
    for (let key in params) {
        url = url.replace(`{${key}}`, params[key]);
    }

    return url;
}; 