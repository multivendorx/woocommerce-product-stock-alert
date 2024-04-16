import React, {useState, useEffect} from "react";
import "./support.scss";

const questions = [
  {
    id: 1,
    question: 'Why am I not receiving any emails when a customer subscribes for an out-of-stock product?',
    answer: 'Please install a plugin like Email Log and perform a test subscription. If the email appears in the Email Log list, it suggests that there might be an issue with your email server. We recommend reaching out to your server administrator to address this matter.',
  },
  {
    id: 2,
    question: 'Why is the out-of-stock form not appearing?',
    answer: 'There might be a theme conflict issue. To troubleshoot, switch to a default theme like Twenty Twenty-Four and check if the form appears.',
  },
  {
    id: 3,
    question: 'Does Product Stock Manager & Notifier support product variations?',
    answer: 'Yes, product variations are fully supported and editable from the Inventory Manager. Product Stock Manager & Notifier handles variable products with ease and uses an expandable feature to make managing variations clear and straightforward.',
  },
  {
    id: 4,
    question: 'Do you support Google reCaptcha for the out-of-stock form?',
    answer: 'Yes, Product Stock Manager & Notifier Pro has support for reCaptcha.',
  },
  
]

function FAQ(props) {    
    const [searchTerm, setSearchTerm] = useState('');
    const [searchResults, setSearchResults] = useState([]);
    const handleSearchChange = e => {
      setSearchTerm(e.target.value);
    };
    
    useEffect(() => {
      const results = props.data.filter(item=>
        item.question.toLowerCase().includes(searchTerm)
      );
      setSearchResults(results);
    }, [searchTerm]);
    
    return (    
      <div className='container'>
        <h2 className="heading">How can we help you?</h2>
        <section className='faq'>
         {searchResults.map(item => <Question question={item.question} answer={item.answer} />)}
        </section>      
      </div>
    )
  }

  const Question = props => {
    const [isActive, setActive] = React.useState(false);
    const handleClick = (id) => {
     setActive(!isActive)
   }
     return(
      <div className="question-wrapper">
      <button onClick={() => handleClick(props.id)} className='question' id={props.id}>
        <h3>{props.question}</h3>
        <div>
           <svg className={isActive? 'active' : ''} viewBox="0 0 320 512" width="100" title="angle-down">
             <path d="M143 352.3L7 216.3c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0l96.4 96.4 96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.2 9.4-24.4 9.4-33.8 0z" />
           </svg>
        </div>     
      </button>
      <div className={isActive? 'answer active' : 'answer'}>{props.answer}</div>
      </div>
     )
   }

const Support = () => {
  const url = "https://www.youtube.com/embed/cgfeZH5z2dM?si=3zjG13RDOSiX2m1b";

  const supportLink = [
    {
      title: "Get in Touch with Support",
      icon: "mail",
      description: "Reach out to the support team for assistance or guidance.",
      link: "https://multivendorx.com/contact-us",
    },
    {
      title: "Explore Documentation",
      icon: "submission-message",
      description: "Understand the plugin and its settings.",
      link: "https://multivendorx.com/docs/knowledgebase/products-stock-manager-notifier-for-woocommerce/",
    },
    {
      title: "Contribute Here",
      icon: "support",
      description: "To participation in product enhancement.",
      link: "https://github.com/multivendorx/woocommerce-product-stock-alert/issues",
    },
  ];


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
          <div className="support-container-wrapper">
            <div className="video-support-wrapper">
              <iframe
                src={url}
                title="YouTube video player"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen
              />
            </div>
            <div className="support-quick-link">
              {supportLink?.map((item, index) => {
                return (
                  <>
                    <div key={index} className="support-quick-link-items">
                      <div className="icon-bar">
                        <i className={`admin-font font-${item.icon}`}></i>
                      </div>
                      <div className="content">
                        <a href={item.link} target="_blank">{item.title}</a>
                        <p>{item.description}</p>
                      </div>
                    </div>
                  </>
                );
              })}
            </div>
          </div>
          <div className="faq-wrapper">
          <FAQ data={questions}/>
          </div>
        </div>
      </div>
    </>
  );
};

export default Support;