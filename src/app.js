import React from 'react';
import { useLocation } from 'react-router-dom';

import Settings from './components/Settings/Settings.jsx';
import { ModuleProvider } from './contexts/ModuleContext.jsx';
import SubscribersList from './components/SubscriberList/SubscribersList.jsx';
import ManageStock from './components/Managestock/Managestock.jsx';
import Import from './components/Managestock/ImportExport/Import.jsx';
import Export from './components/Managestock/ImportExport/Export.jsx';

const Route = () => {
    const currentTab = new URLSearchParams(useLocation().hash);
    return (
        <>
            { currentTab.get('tab') === 'settings' && <Settings initialTab='general' /> }
            { currentTab.get('tab') === 'subscribers-list' && <SubscribersList />}
            { currentTab.get('tab') === 'manage-stock' && <ManageStock /> }
            { currentTab.get('tab') === 'import' && <Import /> }
            { currentTab.get('tab') === 'export' && <Export /> }
        </>
    );
}

const App = () => {
    const currentTabParams = new URLSearchParams(useLocation().hash);
    
    document.querySelectorAll('#toplevel_page_stock-manager>ul>li>a').forEach((menuItem) => {
        const menuItemUrl = new URL(menuItem.href);
        const menuItemHashParams = new URLSearchParams(menuItemUrl.hash.substring(1));

        menuItem.parentNode.classList.remove('current');
        if ( menuItemHashParams.get('tab') === currentTabParams.get('tab')) {
            menuItem.parentNode.classList.add('current');
        }
    });
   
    return (
        <>
            <ModuleProvider modules = {appLocalizer.active_modules}><Route/></ModuleProvider>
        </>
    )
}

export default App;