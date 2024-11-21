import React from 'react';
import { useLocation } from 'react-router-dom';

import Settings from './components/Settings/Settings.jsx';
import SubscribersList from './components/SubscriberList/SubscribersList.jsx';
import ManageStock from './components/Managestock/Managestock.jsx';
import Import from './components/Managestock/ImportExport/Import.jsx';
import Export from './components/Managestock/ImportExport/Export.jsx';

const App = () => {
    const currentUrl = window.location.href;
    document.querySelectorAll('#toplevel_page_stock-manager>ul>li>a').forEach((element) => {
        element.parentNode.classList.remove('current');
        if (element.href === currentUrl) {
            element.parentNode.classList.add('current');
        }
    });

    const location = new URLSearchParams(useLocation().hash);
    return (
        <>
            { location.get('tab') === 'settings' && <Settings initialTab='general' /> }
            { location.get('tab') === 'subscribers-list' && <SubscribersList />}
            { location.get('tab') === 'manage-stock' && <ManageStock /> }
            { location.get('tab') === 'import' && <Import /> }
            { location.get('tab') === 'export' && <Export /> }
        </>
    );
}

export default App;