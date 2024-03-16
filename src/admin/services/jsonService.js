/**
 * Core static JSON service module.
 */

/**
 * Get Setting JSON data as object.
 * @return {Array} Array of Object.
 */
const getSettingsJsonData = () => {
    const settings = {};
    const context = require.context(`../../assets/json/settings`, false, /\.json$/);
    context.keys().forEach((key) => {
        const data = context(key);
        // Add each key-value pair from the data object to the settings object
        Object.keys(data).forEach((dataKey) => {
            settings[dataKey] = data[dataKey];
        });
    });
    return settings;
}

export { getSettingsJsonData };