/**
 * Core static JSON service module.
 */

/**
 * Get Setting JSON data as object.
 * @return {Array} Array of Object.
 */
const getTemplateData = () => {
    const settings = [];
    
    const context = require.context('../template/settings', false, /\.js$/); // Adjust the folder path and file extension
    context.keys().forEach((key) => {
        const module = context(key);
        let fileName = key.substring(key.lastIndexOf('/') + 1, key.lastIndexOf('.'));
        // Check if the module has a default export and push it to the settings array
        if (module && module.default) {
            settings.push( module.default );
        }
    });

    return settings;
};

export { getTemplateData };