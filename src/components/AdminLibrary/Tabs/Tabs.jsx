import { Link } from "react-router-dom";
import Brand from "../../../assets/images/Brand.png";
import BrandSmall from "../../../assets/images/Brand-small.png";
import "./tabs.scss";
import { useState } from "react";

const Tabs = (props) => {
  const {
    tabData,
    currentTab,
    getForm,
    prepareUrl,
    HeaderSection,
    BannerSection,
  } = props;

  const [menuCol, setMenuCol] = useState(false);
  const [openedSubtab, setOpenedSubtab] = useState("");

  const showTabSection = (tab) => {
    return tab.link ? (
      <a href={tab.link}>
        {tab.icon && <i className={`admin-font ${tab.icon}`}></i>}
        {menuCol ? null : tab.name}
      </a>
    ) : (
      <Link
        className={currentTab === tab.id ? "active-current-tab" : ""}
        to={prepareUrl(tab.id)}
      >
        {tab.icon && <i className={` admin-font ${tab.icon} `}></i>}
        {menuCol ? null : tab.name}
        {menuCol
          ? null
          : !appLocalizer.pro_active &&
            tab.proDependent && <span class="admin-pro-tag">Pro</span>}
      </Link>
    );
  };

  const supportLink = [
    {
      title: "Get in touch with Support",
      icon: "mail",
      description: "Reach out to the support team for assistance or guidance.",
      link: "https://multivendorx.com/contact-us/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=productsstockmanager",
    },
    {
      title: "Explore Documentation",
      icon: "submission-message",
      description: "Understand the plugin and its settings.",
      link: "https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=productsstockmanager",
    },
    {
      title: "Contribute Here",
      icon: "support",
      description: "To participation in product enhancement.",
      link: "https://github.com/multivendorx/woocommerce-product-stock-alert/issues",
    },
  ];

  const showHideMenu = (tab) => {
    return (
      <Link
        className={currentTab === tab.id ? "active-current-tab" : ""}
        onClick={(e) => {
          e.preventDefault();
          if (openedSubtab == tab.id) {
            setOpenedSubtab("");
          } else {
            setOpenedSubtab(tab.id);
          }
        }}
      >
        {tab.icon && <i className={` admin-font ${tab.icon} `}></i>}
        {menuCol ? null : tab.name}
        {menuCol ? null : openedSubtab == tab.id ? (
          <p className="tab-menu-dropdown-icon active">
            <i className="admin-font font-keyboard_arrow_down"></i>
          </p>
        ) : (
          <p className="tab-menu-dropdown-icon">
            <i className="admin-font font-keyboard_arrow_down"></i>
          </p>
        )}
      </Link>
    );
  };

  // Get the description of the current tab.
  const getTabDescription = (tabData) => {
    return tabData.map(({ content, type }) => {
      if (type === "file") {
        return (
          content.id === currentTab &&
          content.id !== "support" && (
            <div className="tab-description-start">
              <div className="tab-name">{content.name}</div>
              <p>{content.desc}</p>
            </div>
          )
        );
      } else if (type === "folder") {
        // Get tabdescription from child by recursion
        return getTabDescription(content);
      }
    });
  };

  const handleMenu = () => {
    let menudiv = document.getElementById("current-tab-lists");
    menudiv.classList.toggle("active");
  };

  const handleMenuShow = () => {
    setMenuCol(!menuCol);
  };

  return (
    <>
      <div className={` general-wrapper ${props.queryName} `}>
        {HeaderSection && <HeaderSection />}

        {BannerSection && <BannerSection />}

        <nav className="admin-panel-nav">
          <div className="brand">
            <p>Stock Manager</p>
            <span>by<img src={Brand} alt="logo" /></span>
          </div>
          <button onClick={handleMenu}>
            <i className="admin-font font-menu"></i>
          </button>
        </nav>

        <div
          className={`middle-container-wrapper ${
            props.horizontally ? "horizontal-tabs" : "vertical-tabs"
          }`}
        >
          <div className={`${menuCol ? "showMenu" : ""} middle-child-container`}>
            <div
              id="current-tab-lists"
              className="current-tab-lists"
            >
              <div className="current-tab-lists-container">
                {tabData.map(({ type, content }) => {
                  if (type !== "folder") {
                    return showTabSection(content);
                  }

                  // Tab has child tabs
                  return (
                    <div className="tab-wrapper">
                      {showHideMenu(content[0].content)}
                      {
                        <div
                          className={`subtab-wrapper ${menuCol && "show"} ${
                            openedSubtab == content[0].content.id && "active"
                          }`}
                        >
                          {content.slice(1).map(({ type, content }) => {
                            return showTabSection(content);
                          })}
                        </div>
                      }
                    </div>
                  );
                })}
                <button className="menu-coll-btn" onClick={handleMenuShow}>
                  <span>
                    <i className="admin-font font-arrow-left"></i>
                  </span>
                  {menuCol ? null : "Collapse"}
                </button>
                <button onClick={handleMenu} className="menu-close"><i className="admin-font font-cross"></i></button>
              </div>
            </div>
            <div className="tab-content">
              {/* Render name and description of the current tab */}
              {getTabDescription(tabData)}
              {/* Render the form from parent component for better control */}
              {getForm(currentTab)}
            </div>
          </div>
        </div>

        
        <div className="support-card">
            {supportLink.map((item, index) => {
              return (
                <>
                  <a href={item.link} target="_blank" className="card-item">
                    <i className={`admin-font font-${item.icon}`}></i>
                    <a href={item.link} target="_blank">{item.title}</a>
                    <p>{item.description}</p>
                  </a>
                </>
              );
            })}
          </div>
      </div>
    </>
  );
};

export default Tabs;
