/* global stockManagerAppLocalizer */
import React, { useState, useEffect } from 'react';
import { BrowserRouter as Router, Link } from 'react-router-dom';
import PuffLoader from 'react-spinners/PuffLoader';
import axios from 'axios';
import BannerSection from '../Banner/banner';
import DynamicForm from '../DynamicForm/DynamicForm';

const Tabs = (props) => {
    const [tabs, setTabs] = useState(null);
    const [currentTab, setCurrentTab] = useState(props.initialTab);

    useEffect(() => {
        axios({
            url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager/v1/fetch-admin-tabs`,
        }).then((response) => {
            setTabs(response.data ? JSON.parse(response.data) : null);
        });
    }, []);

    const getTabDescription = () => {
        const { tablabel, description } = tabs[currentTab];
        return (
            <div className="woo-tab-description-start">
				<div className="woo-tab-name">{ tablabel }</div>
				<p>{ description }</p>
			</div>
        );
    }

    const getTabs = () => {
        return Object.entries(tabs).map(([tabName, tabContent]) => {
            return (
                <Link
                    className={ currentTab === tabName ? 'active-current-tab' : ''}
                    onClick={(e) => {
                        e.preventDefault();
                        setCurrentTab(tabName)
                    }}
                >
                    { tabContent.icon && <i className={`${ tabContent.icon }`}></i> }
                    { tabContent.tablabel }
                    {
                        ( stockManagerAppLocalizer.pro_active == 'free' ) &&
                        ( tabName == 'email' || tabName == 'mailchimp' ) &&
                            <span class="stock-manager-pro-tag">Pro</span> 
                    }
                </Link>
            );
        });
    }  

    return (
        <>
            <div className={`woo-general-wrapper woo-${currentTab}`}>
                {stockManagerAppLocalizer.pro_active === 'free' && <BannerSection />}
                {
                    <div className="woo-container woo-tab-banner-wrap">
                        <div className={`woo-middle-container-wrapper woo-vertical-tabs`}>
                            { tabs && getTabDescription() }
                            <div className="woo-middle-child-container">
                                <div className="woo-current-tab-lists">
                                    { tabs && getTabs() }
                                </div>
                                <div className="woo-tab-content">
                                    {
                                        tabs ? <DynamicForm
                                            currentTab={currentTab}
                                            tabs={tabs}
                                            setTabs={setTabs}
                                        />
                                            :
                                        <PuffLoader
                                            color={'#cd0000'}
                                            size={200}
                                            loading={true}
                                        />
                                    }
                                </div>
                            </div>
                        </div>
                    </div>
                }
			</div>
        </>
    );
}

export default Tabs;