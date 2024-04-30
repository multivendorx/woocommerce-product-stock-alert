import React, { useState , useEffect, useRef } from "react";
import axios from "axios";
import { __ } from "@wordpress/i18n";
import Dropdown from "./Dropdown";
import Input from "./Input";

const ProductTable = ( { products, headers, setData, setDisplayMessage, rowsPerPage, setRowsPerPage, currentPage, setCurrentPage, totalProducts } ) => {
  const updateDataUrl = `${ appLocalizer.apiUrl }/stockmanager/v1/update-product`;
  const [ event, setEvent ] = useState();
  const [ inputChange, setInputChange ] = useState( false );
  const [ expandElement , setExpandElement ] = useState( {} );
  const [ expandedRows, setExpandedRows ] = useState( {} );
  const [ uploadData, setUploadData ] = useState( {
    id: "",
    name: "",
    value: "",
  });

  const [activeInput, setActiveInput] = useState({});
  const pageCount = totalProducts ? Math.ceil(totalProducts / rowsPerPage) - 1 : 0;

  useEffect(() => {
    document.addEventListener('click', handleDocumentClick);
    return () => {
      document.removeEventListener('click', handleDocumentClick);
    };
  });

  //Function to Toggle the Expandable rows for the variable products
  const toggleRow = ( productId ) => {
    setExpandedRows( ( prevExpandedRows ) => ( {
      ...prevExpandedRows,
      [ productId ]: !prevExpandedRows[ productId ],
    } ) );
  };

  //Function to Toggle the Dropdown when the screen size is small
  const toggleActive = ( productId ) => {
    if ( Object.keys( expandElement ) == productId ) {
      setExpandElement( (prevExpandElement) => ( {
        ...prevExpandElement,
        [ productId ]: !prevExpandElement[ productId ],
      } ) );
    } else {
      setExpandElement( { [ productId ] : true } )
    }
  };

  // Function to save the manage_stock, backorders, stock_status when
  // their data is updated in the uploadData state
  useEffect( () => {
    const submitData = async () => {
      let name = uploadData.name
      if ( name === "set_manage_stock" || name === "set_backorders" || name === "set_stock_status" ) {
        changeData();
      }
    };    
    submitData();
  }, [ uploadData ] );

  // Function to upload the data in the state variable
  function updateData( id, name, value ) {
    setUploadData({
      ["id"]: id,
      ["name"]: name,
      ["value"]: value,
    } );
  }

  // Function to ulpoad the 
  function changeData() {
    if (!uploadData.id) {
      return;
    }

    axios( {
      method: "post",
      url: updateDataUrl,
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: uploadData,
    } ).then( ( response ) => {
     
    } )
    setDisplayMessage( 'Settings Saved' );
    setTimeout( () => {
      setDisplayMessage( '' );  
    }, 2000 );
  }
  
  // Function to handle changes in the rows per page select input
  const handleRowsPerPageChange = ( e ) => {
    setCurrentPage( 0 );
    setRowsPerPage( parseInt( e.target.value ) );
    window.scrollTo( {
      top: 0,
      behavior: 'smooth',
    } );
  };

  const inputFieldOnClick = (e, productId, headerKey) => {
    if ( uploadData.id ) {
      setActiveInput({});
      changeData();
      updateData('', '', '');
      setInputChange( false );
    }

    let inputElement = e.target;
    setActiveInput({ id: productId, key: headerKey });
    setEvent(inputElement);
  }

  const handleDocumentClick = (e) => {
    const inputClicked = e.target.classList.contains('edit-input');
    if ( inputClicked ) return;
    
    document.removeEventListener('click', handleDocumentClick);
    
    if (Object.keys(activeInput).length) {
      setActiveInput({});
      changeData();
      updateData('', '', '');
      setInputChange( false );
    }
  };

  const handleChange = ( e, id, key, type ) => {
    let element = e.target;
    let updateKey = 'set_' + key;
    let Value = ( updateKey === "set_manage_stock" ) ? element.checked : element.value;
    if( updateKey == 'set_manage_stock' ) {
      updateData( id, updateKey, Value.toString() );
    } else {
      updateData( id, updateKey, Value );
    }
    if ( updateKey === 'set_stock_quantity' && Value > 0 ) {
      const stock_status = element.parentElement.parentElement.parentElement.querySelector( '.product_stock_status' ).children[0].children[1];
      stock_status.classList.remove( 'outofstock' );
      stock_status.classList.add( 'instock' );
      stock_status.innerHTML = 'In stock';
    }else if ( updateKey === 'set_stock_quantity' && Value <= 0 ) {
      const stock_status = element.parentElement.parentElement.parentElement.querySelector( '.product_stock_status' ).children[0].children[1];
      stock_status.classList.add( 'outofstock' );
      stock_status.classList.remove( 'instock' );
      stock_status.innerHTML = 'Out of stock';
    }

    if ( updateKey === "set_sale_price" ) {
      const regular_price = Number(
        element.parentElement.parentElement.parentElement.querySelector( '.product_regular_price' ).children[0].children[1].value
      );
      if (Value >= regular_price) {
        setDisplayMessage( "Sale price cannot be greater than regular price" );
      } else {
        setDisplayMessage( '' );
      }
    }
    if ( updateKey === "set_regular_price" ) {
      const sale_price = element.parentElement.parentElement.parentElement.querySelector('.product_sale_price').children[0].children[1].value;
      if ( Number( sale_price ) > Number( Value ) ) {
        setData( ( prevData ) => {
          const newData = { ...prevData };
          newData[ id ][ "sale_price" ] = "0";
          return newData;
        } );
      }
    }
    if ( updateKey === "set_manage_stock" && Value === true ) {
      if ( type === "Variation" ) {
        let parent_id = element.id;
        setData( ( prevData ) => {
          const newData = { ...prevData };
          newData[parent_id]["variation"][id]["stock_quantity"] = 0;
          return newData;
        } );
      } else {
        setData( (prevData ) => {
          const newData = { ...prevData };
          newData[ id ][ "stock_quantity" ] = "0";
          return newData;
        } );
      }
    }
    if ( updateKey !== "set_manage_stock" && updateKey !== "set_backorders" && updateKey !== "set_stock_status" ) {
      setInputChange( true );
      setEvent( e.target );
      Value = Value.replace( /^0+/, "" );
    }
    if ( type === "Variation" ) {
      let parent_id = element.id;
      setData( ( prevData ) => {
        const newData = { ...prevData };
        newData[ parent_id ][ "variation" ][ id ][ key ] = Value;
        return newData;
      } );
    } else { 
      setData( ( prevData ) => {
        const newData = { ...prevData };
        newData[ id ][ key ] = Value;
        return newData;
      } );
    }
  };

  const renderHeader = () => {
    return(
      <tr className="table-head">
        {
          Object.values( headers ).map( ( header ) => {
            return  header.type && <td className={`table-row ${ header.class }`}>{ header.name }</td>
          })
        }
      </tr>
    )
  }
  const renderSingleRow = ( productId, product ) => {
    return (
      <tr className={`${ expandElement[ productId ]? 'active' : "" } ${ product.type == 'Variation' ? 'expand' : "" }`}>
        {
          Object.entries( headers ).map( ( [ headerKey, header ] ) => {
            switch( header.type ) {
              case 'expander':
                if (product[ header.dependent ]) {
                  return(
                    <td className={ `table-row ${ header.class} ` }>
                      <div onClick={ () => toggleRow( productId ) } className="table-data-container">
                        <button class="setting-btn">
                          <span class="bar bar1"></span>
                          <span class="bar bar2"></span>
                          <span class="bar bar1"></span>
                        </button>
                      </div>
                    </td>
                  );
                } else {
                  return (
                    <td className={ `table-row ${ header.class }` }>
                      <div className="table-data-container disable">
                        <button disabled class="setting-btn">
                            <span class="bar bar1"></span>
                            <span class="bar bar2"></span>
                            <span class="bar bar1"></span>
                        </button>
                      </div>
                    </td>
                  )
                }
              case 'product':
                return(
                  <td className={`table-row ${ header.class }`}>
                    <div className="table-data-container">
                        <h1>{ header.name }</h1>
                        <a href={ product[ header.dependent ] } target="_blank">
                          <img src={ product[ "image" ] } class="table-image" />
                        </a>
                        <Input 
                          handleChange={(e) => { handleChange( e, product.id, "name", product.type ) } }
                          inputFieldOnClick={(e) => { inputFieldOnClick( e, product.id, "name" ) } }
                          headerKey={ "name" } 
                          product={ product } 
                          header={header}
                          type='text'
                          active={activeInput.id == product.id && activeInput.key == "name" }
                      />
                      </div>
                  </td>
                );
              case 'checkbox':
                return (
                  <td className={ `table-row ${ header.class }` }>
                    <div className="table-data-container">
                        <h1>{ header.name }</h1>
                        <div className="custome-toggle-default">                          
                          <input 
                            id={ product.type === 'Variation' ? product.parent_product_id : "" } 
                            onChange={ ( e ) => { handleChange( e, product.id, headerKey, product.type ) } } 
                            checked={ product[ headerKey ] } 
                            type="checkbox" 
                          />
                          <label></label>
                        </div>
                      </div>
                  </td>
                );
              case 'dropdown':
                return (
                  <td className={ `table-row ${ header.class }` }>
                    <div className="table-data-container">
                        <h1>{ header.name }</h1>
                        <Dropdown 
                          handleChange={ ( e) => { handleChange( e, product.id, headerKey, product.type ) } } 
                          options={ header.options } 
                          headerKey={ headerKey } 
                          product={ product } 
                          header={ header } 
                        />
                      </div>
                  </td>
                )
              case 'text':
              case  'number':
                return (
                  <td className={ `table-row ${ header.class }` }>
                    <div className="table-data-container">
                      <Input 
                        handleChange={ ( e ) => { handleChange( e, product.id, headerKey, product.type ) } }
                        inputFieldOnClick={(e) => { inputFieldOnClick( e, product.id, headerKey ) } }
                        headerKey={ headerKey } 
                        product={ product } 
                        header={header}
                        active={activeInput.id == product.id && activeInput.key == headerKey}
                      />
                    </div>
                  </td>
                )
              case 'rowExpander':
                return(
                  <td className={ `${ expandElement[ productId ] ? 'active' : "" } ${ header.class } table-row` }>
                    <button onClick={ ()=> toggleActive( productId ) }>
                      <svg xmlns="http://www.w3.org/2000/svg" class="bi bi-arrow-right-short" viewBox="0 0 16 16" >
                        <path fill-rule="evenodd" d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8"/>
                      </svg>
                    </button>
                  </td>
                )
            }
          })
        }
      </tr>
    )
  }

  const renderRows = () => {
    return (
      <>
        {
          Object.entries( products ).map( ( [ productId, product ] ) => {
            return (
              <React.Fragment key={ productId }>
                { renderSingleRow( productId, product ) }
                {expandedRows[ productId]  && product.variation && Object.entries( product.variation ).map( ( [ variationProductId, variationProduct ] ) => (
                  <React.Fragment key={ variationProductId }>
                    { renderSingleRow( variationProductId, variationProduct ) }
                  </React.Fragment>
                ))}
              </React.Fragment>
            );
          })
        }
      </>
    );
  };
  
  
  return (
    <React.Fragment>
      <table>
        <thead>
            { renderHeader() }
        </thead>
        <tbody>
          {
            Object.keys(products).length ?
              renderRows() :
              <p className="no-data-message">There are no records to display</p>
          }
        </tbody>
      </table>
      <div className="pagination-section">
        <div className="pagecount-select-wrapper">
          <p className="show-count-text">Rows per page:</p>
          <select
            className="page-count-select"
            value={rowsPerPage}
            onChange={handleRowsPerPageChange}
          >
            {
              [10, 25, 50, 100].map((rowsPerPage) => {
                return <option value={rowsPerPage}>{rowsPerPage}</option>
              })
            }
          </select>
        </div>
        <div className="page-handle-wrapper">
          <p className="show-page-count">
            {(currentPage) * rowsPerPage + 1}-{Math.min((currentPage + 1) * rowsPerPage, totalProducts )} of {totalProducts}
          </p>
          <div className="page-handle-button">
            {/* Set current page to 0 */}
            <button
              className={ currentPage === 0 ? 'deactive' : '' }
              onClick={(e) => {
                if (currentPage > 0) {
                  setCurrentPage(0);
                }
              }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" role="presentation"><path d="M18.41 16.59L13.82 12l4.59-4.59L17 6l-6 6 6 6zM6 6h2v12H6z"></path><path fill="none" d="M24 24H0V0h24v24z"></path></svg>
            </button>
            {/* Set current page to less 1 */}
            <button
              className={currentPage === 0 ? 'deactive' : ''}
              onClick={(e) => {
                if (currentPage > 0) {
                  setCurrentPage( currentPage - 1 );
                }
              }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" role="presentation"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
            </button>
            {/* Set current page to more 1 */}
            <button
              className={currentPage === pageCount ? 'deactive' : ''}
              onClick={(e) => {
                if (currentPage < pageCount) {
                  setCurrentPage( currentPage + 1 );
                }
              }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" role="presentation"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path><path d="M0 0h24v24H0z" fill="none"></path></svg>
            </button>
            {/* Set current page to maximum nuber of page */}
            <button
              className={currentPage === pageCount ? 'deactive' : ''}
              onClick={(e) => {
                if (currentPage < pageCount) {
                  setCurrentPage( pageCount );
                }
              }}
            >
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" role="presentation"><path d="M5.59 7.41L10.18 12l-4.59 4.59L7 18l6-6-6-6zM16 6h2v12h-2z"></path><path fill="none" d="M0 0h24v24H0V0z"></path></svg>
            </button>
          </div>
        </div>
      </div>
    </React.Fragment>
  );
};
export default ProductTable;