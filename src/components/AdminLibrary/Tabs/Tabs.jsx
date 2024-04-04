import { Link } from 'react-router-dom';
import Brand from '../../../assets/images/Brand.png';
import BrandSmall from '../../../assets/images/Brand-small.png';
import "./tabs.scss";
import { useState } from 'react';

const Tabs = ( props ) => {
    const { tabData, currentTab, getForm, prepareUrl, HeaderSection, BannerSection } = props;

    const [menuCol, setMenuCol] = useState(false);
    const [openedSubtab, setOpenedSubtab] = useState('');

    const showTabSection = (tab) => {
        return tab.link ? (
            <a href={ tab.link }>
                { tab.icon && <i className={`mvx-font ${ tab.icon }`}></i> }
                { menuCol ? null : tab.name }
            </a>
        ) : (
            <Link
                className={ currentTab === tab.id ? 'active-current-tab' : '' }
                to={ prepareUrl( tab.id ) }
            >
                { tab.icon && <i className={` mvx-font ${ tab.icon } `} ></i> }
                { menuCol ? null : tab.name }
                { menuCol  ? null :
                    ( appLocalizer.pro_active == 'free' ) && tab.proDependent &&
                    <span class="admin-pro-tag">Pro</span> 
                }
            </Link>
        );
    }

    const showHideMenu = ( tab ) => {
        return <Link
            className={currentTab === tab.id ? 'active-current-tab' : ''}
            onClick={(e) => {
                e.preventDefault();
                if (openedSubtab == tab.id) {
                    setOpenedSubtab('');
                } else {
                    setOpenedSubtab(tab.id);
                }
            }}
        >
            { tab.icon && <i className={` mvx-font ${ tab.icon } `} ></i> }
            {menuCol ? null : tab.name}
            {
                openedSubtab == tab.id ? 
                    <p>Up</p>
                    :
                    <p>Down</p>
            }
        </Link>
    }
    
    // Get the description of the current tab.
    const getTabDescription = () => {

        return tabData.map( ( tab ) => {
            return  tab.id === currentTab &&
                <div className="mvx-tab-description-start">
                    <div className="mvx-tab-name">{ tab.name }</div>
                    <p>{ tab.desc }</p>
                </div>
        });
    }

    const handleMenu =()=>{
        let menudiv = document.getElementById('mvx-current-tab-lists');
        menudiv.classList.toggle('active');
    }

    const handleMenuShow = () => {
        setMenuCol(!menuCol);
    }
    
    return (
        <>
            <div className={` mvx-general-wrapper mvx-${ props.queryName } `}>
                { HeaderSection && <HeaderSection />}
                <div className="mvx-container">
                
                { BannerSection && <BannerSection />}

                <nav className='admin-panel-nav'>
                    <button onClick={handleMenu}><i className='mvx-font font-menu'></i></button>
                    <div className='brand'>
                        <img src={Brand} alt="logo" />
                    </div>
                </nav>

                    <div
                        className={ `mvx-middle-container-wrapper ${
                            props.horizontally
                                ? 'mvx-horizontal-tabs'
                                : 'mvx-vertical-tabs'
                        }`}
                    >
                        <div className="mvx-middle-child-container">
                            <div id='mvx-current-tab-lists' className={`${menuCol ? 'showMenu' : ''} mvx-current-tab-lists`}>
                                <div className='mvx-current-tab-lists-container'>
                                    <div className='brand'>
                                        {menuCol ? <img src={BrandSmall} alt="logo" /> : <img src={Brand} alt="logo" />}
                                    {menuCol ? null : <p>Stock Manager</p>}
                                        <button onClick={handleMenu} className='menu-close'><i className='mvx-font font-cross'></i></button>
                                    </div>

                                    {
                                        tabData.map( ( { type, content } ) => {
                                            
                                            if (type !== 'folder') {
                                                return showTabSection(content)
                                            }

                                            // Tab has child tabs
                                            return <div className='tab-wrapper'>
                                                {
                                                    showHideMenu(content[0].content)
                                                }
                                                {
                                                    // openedSubtab == content[0].content.id &&
                                                    <div
                                                        className={`subtab-wrapper ${menuCol ? '????' : ''}`}
                                                        style={{ display: !openedSubtab ? 'none' : '' }}
                                                    >
                                                        {
                                                            content.slice(1).map(({ type, content }) => {
                                                                return showTabSection(content);
                                                            })
                                                        }
                                                    </div>
                                                }
                                            </div>
                                        })
                                    }
                                    <button className='menu-coll-btn' onClick={handleMenuShow}><span><i className='mvx-font font-arrow-left'></i></span>{menuCol ? null : 'Collapse'}</button>
                                </div>
                            </div>
                            <div className="mvx-tab-content">
                                {/* Render name and description of the current tab */}
                                { getTabDescription() }
                                {/* Render the form from parent component for better controll */}
                                { getForm( currentTab )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}

export default Tabs;