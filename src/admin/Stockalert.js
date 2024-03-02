import React from 'react';
import { useLocation } from 'react-router-dom';
import WOOTab from './Tabs/Tab.jsx';
import SubscribersList from './SubscriberList/SubscribersList.jsx';
import ManageStock from './Managestock/managestock.jsx'
import Import from './Managestock/Import.jsx';
import Export from './Managestock/Export.jsx';
const Stockalert = () => {
    const currentUrl = window.location.href;
    document.querySelectorAll('#toplevel_page_woo-stock-manager-setting>ul>li>a').forEach((element) => {
        element.parentNode.classList.remove('current');
        if (element.href === currentUrl) {
            element.parentNode.classList.add('current');
        }
    });

    const location = new URLSearchParams(useLocation().hash);

    return (
        <>
            { location.get('tab') === 'settings' && <WOOTab initialTab='general' /> }
            { location.get('tab') === 'subscribers-list' && <SubscribersList />}
            { location.get('tab') === 'manage-stock' && <ManageStock /> }
            { location.get('tab') === 'import' && <Import /> }
            { location.get('tab') === 'export' && <Export /> }
        </>
    );
}

export default Stockalert;