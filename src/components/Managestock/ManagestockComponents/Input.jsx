import React from 'react';
import { __ } from "@wordpress/i18n";

export default function Input( { headerKey, header, product, handleChange, editButtonOnClick, inputFieldOnClick, active, type } ) {
    //Functional component to return the edit icon
    const GetIcon = () => {
        return (
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit">
              <path fill="#212121"d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"/>
            </svg>
          );
    }

    //Functional component to render the input element
    function RenderInput( button , value , type ) {
        return(
            <>
                    <h1>{ header.name }</h1>
                    {
                        button ?
                           <>
                            <input
                                className={ `${active ? 'active' : ''} edit-input` }
                                onClick={inputFieldOnClick}
                                id={product.type === 'Variation' ? product.parent_product_id : null}
                                onChange={handleChange}
                                type={type}
                                value={(value === "" || value === null ? 0 : value)}
                                autofocus="false"
                                readOnly={ ! active }
                            />
                            <button onClick={ editButtonOnClick } className="edit-btn-product">
                                { GetIcon() }
                            </button>
                           </>
                        :
                            <p>{ ( value === "" || value === null ? 0 : value ) }</p>
                    }
            </>
        )
    }
    if( header.stockStatusDependent != undefined ) {
        return RenderInput( product[ header.stockStatusDependent ] , product[ headerKey ] , header.type )
    }else{
        return RenderInput( header.editable , product[ headerKey ] , type || header.type );
    }
}