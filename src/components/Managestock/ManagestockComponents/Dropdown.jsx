import React from 'react';
import { __ } from "@wordpress/i18n";

//component to show all the dropdown elements
export default function Dropdown( { product , header , headerKey , options , handleChange } ) {
  const dropdown = () => {
    return(
      <select className={`${ product[ headerKey ] }`} value={ product[ headerKey ] } id={ product.type === 'Variation' ? product.parent_product_id : null } onChange={ handleChange }>
        {
          Object.entries( options ).map( ( [ key,value] ) => {
            return(
              <option className={ key } value={ key }>{ value }</option>
            )
          } )
        }
      </select>
    );
  }
  if( header.falsedependent != undefined && !product[ header.falsedependent ] ) {
    return dropdown();
  } else if ( header.dependent != undefined && product[ header.dependent] ) {
    return dropdown();
  } else {
    return <p className={`${ header.class } ${ product [ headerKey ] }`}> { __ ( options[ product[ headerKey ] ], "woocommerce-stock-manager" ) } </p>;
  }
}
