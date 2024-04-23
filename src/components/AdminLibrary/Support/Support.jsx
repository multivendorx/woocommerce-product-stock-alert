import React, { useState, useEffect } from "react";
import "./support.scss";

const Support = () => {
  const url = "https://www.youtube.com/embed/cgfeZH5z2dM?si=3zjG13RDOSiX2m1b";

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

  const [faqs, setFaqs] = useState([
    {
      question:
        "Why am I not receiving any emails when a customer subscribes for an out-of-stock product?",
      answer:
        "Please install a plugin like Email Log and perform a test subscription.",
      open: true,
    },
    {
      question: "Why is the out-of-stock form not appearing?",
      answer:
        "There might be a theme conflict issue. To troubleshoot, switch to a default theme like Twenty Twenty-Four and check if the form appears.",
      open: false,
    },
    {
      question:
        "Does Product Stock Manager & Notifier support product variations?",
      answer:
        "Yes, product variations are fully supported and editable from the Inventory Manager. Product Stock Manager & Notifier handles variable products with ease and uses an expandable feature to make managing variations clear and straightforward.",
      open: false,
    },
    {
      question: "Do you support Google reCaptcha for the out-of-stock form?",
      answer:
        'Yes, <a href="https://multivendorx.com/woocommerce-product-stock-manager-notifier-pro/?utm_source=WordPressAdmin&utm_medium=PluginSettings&utm_campaign=productsstockmanager" target="_blank">Product Stock Manager & Notifier Pro</a> has support for reCaptcha.',
      open: false,
    },
  ]);

  const toggleFAQ = (index) => {
    setFaqs(
      faqs.map((faq, i) => {
        if (i === index) {
          faq.open = !faq.open;
        } else {
          faq.open = false;
        }

        return faq;
      })
    );
  };

  return (
    <>
      <div className="dynamic-fields-wrapper">
        <div className="support-container">
          <div className="support-header-wrapper">
            <h1 className="support-heading">
              Thank you for using Product Stock Manager & Notifier for
              WooCommerce
            </h1>
            <p className="support-subheading">
              We want to help you enjoy a wonderful experience with all of our
              products.
            </p>
          </div>
          <div className="support-card">
            {supportLink.map((item, index) => {
              return (
                <>
                  <div className="card-item">
                    <i className={`admin-font font-${item.icon}`}></i>
                    <a href={item.link}>{item.title}</a>
                    <p>{item.description}</p>
                  </div>
                </>
              );
            })}
          </div>
          <div className="video-faq-wrapper">
            <div className="video-section">
              <iframe
                src={url}
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
              />
            </div>
            <div className="faq-section">
              <div className="faqs">
                {faqs.map((faq, index) => (
                  <div
                    className={"faq " + (faq.open ? "open" : "")}
                    key={index}
                    onClick={() => toggleFAQ(index)}
                  >
                    <div className="faq-question">{faq.question}</div>
                    <div className="faq-answer" dangerouslySetInnerHTML={{__html: faq.answer}}></div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default Support;
