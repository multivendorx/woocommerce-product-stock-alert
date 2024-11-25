import React from 'react';
import { useLocation } from 'react-router-dom';

import Settings from './components/Settings/Settings.jsx';
import SubscribersList from './components/SubscriberList/SubscribersList.jsx';
import ManageStock from './components/Managestock/Managestock.jsx';
import Import from './components/Managestock/ImportExport/Import.jsx';
import Export from './components/Managestock/ImportExport/Export.jsx';

const App = () => {
    const location = new URLSearchParams( useLocation().hash );

    document.querySelectorAll('#toplevel_page_stock-manager>ul>li>a').forEach((element) => {
        const urlObject = new URL(element.href);
        const hashParams = new URLSearchParams(urlObject.hash.substring(1));

        element.parentNode.classList.remove('current');
        if ( hashParams.get('tab') === location.get('tab')) {
            element.parentNode.classList.add('current');
        }
    });

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