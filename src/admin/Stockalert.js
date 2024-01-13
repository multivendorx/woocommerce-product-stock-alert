import React from 'react';
import { useLocation } from 'react-router-dom';
import WOOTab from './Tabs/Tab.jsx';
import SubscriberList from './Subscriber/subscriber.js';

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
            { location.get('tab') === 'subscriber-list' && <SubscriberList /> }
        </>
    );
}

export default Stockalert;