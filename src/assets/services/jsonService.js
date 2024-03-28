/**
 * Core static JSON service module.
 */

/**
 * Get Setting JSON data as object.
 * @return {Array} Array of Object.
 */
const getSettingsJsonData = () => {
    const settings = [];
    const context = require.context('../json/settings', false, /\.js$/); // Adjust the folder path and file extension
    context.keys().forEach((key) => {
        const module = context(key);
        let fileName = key.substring(key.lastIndexOf('/') + 1, key.lastIndexOf('.'));
        // Check if the module has a default export and push it to the settings array
        if (module && module.default) {
            settings[fileName] = module.default;
        }
    });
    return settings;
};

export { getSettingsJsonData };