import React,{useState,useEffect} from 'react';
import axios from 'axios';
import { __ } from '@wordpress/i18n';

const TableRow = ({ row ,setData ,event , setEvent}) => {
    const updateDataUrl = `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/update`;
    const [ inputChange, setInputChange ] = useState(false);
    const [ uploadData, setUploadData ] = useState({
        id: '',
        name: '',
        value: '',
    });
    useEffect(() => {
            let name = uploadData.name
            if( name !== "" || name === "set_manage_stock" || name === "set_backorders" || name === "stock_status" ){
                changeData();
            }
    }, [uploadData]);

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    function changeData(){
        axios({
            method: 'post', 
            url: updateDataUrl,
            headers: { 'X-WP-Nonce' : stockManagerAppLocalizer.nonce },
            data: uploadData,
        })
    }
    function updateData(id,name,value){
        setUploadData({
            ['id']: id,
            ['name']: name,
            ['value']: value,
        })
    }

    const handleDocumentClick = (e) => {
        if(inputChange){
            changeData();
            setInputChange(false);
            event.classList.add('input-field-edit');
            event.setAttribute('readonly', 'readonly');
            document.removeEventListener('click', handleDocumentClick );
        }
    }
    const editButtonOnClick = (e) => {
        let element = e.currentTarget;
        element.previousSibling.focus();
        element.previousSibling.removeAttribute('readonly');
        element.previousSibling.classList.remove('input-field-edit');
    }
    
    const handleInputMouseOut = (e) => {
        e.currentTarget.children[1].style.display = 'none';
        if(inputChange){
            document.addEventListener( 'click', handleDocumentClick );
        }
    }

    const handleInputMouseOver = (e) => {
        e.currentTarget.children[1].style.display = 'flex';
    }

    const handleChange = (e,id,str,type) => {
        let element = e.target;
        let name = element.name;
        let Value = ( name === "set_manage_stock" ) ? element.checked : element.value;

        updateData(id,name,Value);
        if(name === "set_sale_price"){
            const regular_price = Number(element.parentElement.parentElement.parentElement.children[5].children[0].children[0].value)
            if(Value>=regular_price){
                element.parentElement.children[2].style.display = "block";
            }else{
                element.parentElement.children[2].style.display = "none";
            }
        }
        if(name === 'set_regular_price'){
            const sale_price = element.parentElement.parentElement.nextSibling.children[0].children[0].value;
            if(Number(sale_price)>Number(Value)){
                setData((prevData) => {
                    const newData = { ...prevData };
                    newData[id]['product_sale_price'] = "0";
                    return newData;
                });
            }
        }
        // if(name === 'set_manage_stock' && Value === true){
        //     if(type === 'variation'){
        //         setVariationData(element.id,id,"product_stock_quantity",0)
        //     }
        //     setData((prevData) => {
        //         return prevData.map((obj) => {
        //             if( obj.product_id === id ){
        //                 return {...obj, ["product_stock_quantity"] : 0 };
        //             }
        //             return obj;
        //         })
        //     });
        // }
        if( name !== "set_manage_stock" && name !== "set_backorders" && name !== "stock_status" ){
            setInputChange(true);
            setEvent(e.target)
            Value= Value.replace(/^0+/, "");
        }
        // if(type === "variation"){
        //     setVariationData(element.id,id,str,Value)
        // }else{
            setData((prevData) => {
                const newData = { ...prevData };
                newData[id][str] = Value;
                return newData;
            });
        // }
    }  
  return (
    <div className="custom-container custom-container-col">
        <div className="custom-container-inner-div left">
            <div className="cell div-expand-icon">
            {/* Expandable Row */}
            {
                row.variation?
                    <button className="variable-product-expand">
                        <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        class="bi bi-arrow-right-short"
                        viewBox="0 0 16 16"
                        >
                        <path
                            fill-rule="evenodd"
                            d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8"
                        />
                        </svg>
                    </button>
                :""
            }
            </div>
            <div className="cell div-img">
                {/* Product Image */}
                <a href={row.product_link} target="_blank">
                    <img src={row.product_image} class="table-image"/>
                </a>
            </div>
            <div className="cell div-name" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                {/* Product Name */}
                {
                    (row.product_type === "variation") ?
                        <input  type="text" class={`input-field input-field-edit`}  value={row.product_name} readOnly/>
                    :   <>
                        <input id={( row.product_type === "variation" ) ? row.parent_product_id : row.product_id} type="text" class="input-field input-field-edit" value={row.product_name} name={"set_name"} onChange={(e) => {handleChange(e, row.product_id, "product_name", row.product_type)}} readOnly />
                        <span onClick={editButtonOnClick} class="edit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                        </span>
                        </>
                }
            </div>
        </div>
        <div className="custom-container-inner-div right">
            <div className="cell div-sku" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                {/* Product SKU */}
                <input id={(row.product_type === "variation") ? row.parent_product_id : null } type="text" class="input-field input-field-edit" value={row.product_sku} name={"set_sku"} onChange={(e) => {handleChange(e, row.product_id, "product_sku", row.product_type)}} readOnly />
                <span onClick={editButtonOnClick} class="edit-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                </span>
            </div>
            <div className="cell div-product-type" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                {capitalizeFirstLetter(row.product_type)}
            </div>
            <div class="cell div-regular-price" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
            {/* Product Regular Price */}
                <input id={(row.product_type === "variation") ? row.parent_product_id : row.product_id } type="number" min={0} class="input-field input-field-edit"  value={(row.product_regular_price === "" || row.product_regular_price === null) ? 0 : row.product_regular_price} name={"set_regular_price"} onChange={(e) => {handleChange(e, row.product_id, "product_regular_price",row.product_type)}} readOnly />                        
                <span onClick={editButtonOnClick} className='edit-btn'>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                </span>
            </div>
            <div class="cell div-sale-price" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                {/* Product Sale Price */}
                <input id={(row.product_type === "variation") ? row.parent_product_id : row.product_id} type="number" min={0} class="input-field input-field-edit"  value={( row.product_sale_price === "" ) ? 0 : row.product_sale_price} name={"set_sale_price"} onChange={(e) => { handleChange( e, row.product_id, "product_sale_price", row.product_type ) }} readOnly />                        
                <span onClick={editButtonOnClick} class="edit-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                </span>
                <div className='sale-price-error-message'>
                    Please enter in a value less than the regular price
                </div>
            </div>
            <div className="cell div-manage-stock">
                {/* Manage Stock */}
                <input id={( row.product_type === "variation" ) ? row.parent_product_id : row.product_id }  type="checkbox" name={"set_manage_stock"} checked={row.product_manage_stock} onChange={(e) => {handleChange( e, row.product_id, "product_manage_stock", row.product_type )}} />
                <label htmlFor={row.product_id}></label>
            </div>
            <div className="cell div-stock-status">
                {/* Stock Status */}
                {
                    (row.product_manage_stock)?
                        ( row.product_stock_quantity > 0 || row.variation_stock_quantity > 0 )?
                            <p class="value-sucess">{__("In stock","woocommerce-stock-manager-pro")}</p>:
                            <p class="value-danger">{__("Out of stock","woocommerce-stock-manager-pro")}</p>
                    :
                        <div className='custom-select'>
                            <select id={(row.product_type === "variation") ? row.parent_product_id : null }   name='stock_status' value={row.product_stock_status} onChange={(e) => {handleChange( e, row.product_id, "product_stock_status", row.product_type )}} >
                                <option value={"instock"}>{__("In stock","woocommerce-stock-manager-pro")}</option>
                                <option value={"onbackorder"}>{__("On backorder","woocommerce-stock-manager-pro")}</option>
                                <option value={"outofstock"}>{__("Out of stock","woocommerce-stock-manager-pro")}</option>
                            </select>
                        </div>
                }
            </div>
            <div className="cell div-backorder">
                {/* Backorders */}
                {
                    (row.product_manage_stock)?
                    <div class='custom-select'>
                        <select id={(row.product_type === "variation") ? row.parent_product_id : row.product_id }  name='set_backorders' value={row.product_backorders} onChange={(e) => {handleChange(e, row.product_id, "product_backorders", row.product_type )}}>
                            <option value={"no"}>{__("No","woocommerce-stock-manager-pro")}</option>
                            <option value={"notify"}>{__("Notify","woocommerce-stock-manager-pro")}</option>
                            <option value={"yes"}>{__("Yes","woocommerce-stock-manager-pro")}</option>
                        </select>
                    </div>
                    :
                    <p>{__("No","woocommerce-stock-manager-pro")}</p>
                }
            </div>
            <div className="cell div-stock">
                {/* Stock Quantity */}
                {
                    ( row.product_type === "simple" && row.product_manage_stock )?
                    <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                        <input id={( row.product_type === "variation" ) ? row.parent_product_id : row.product_id } type="number" min={0} class={`input-field input-field-edit ${( row.product_stock_quantity > 0 ) ? "value-sucess" : "value-danger"}`}  value={( row.product_stock_quantity === null || row.product_stock_quantity === "" ) ? '0' : row.product_stock_quantity} name={"set_stock_quantity"} onChange={(e) => {handleChange(e, row.product_id, "product_stock_quantity", row.product_type)}} readOnly/>                        
                        <span onClick={editButtonOnClick} class="edit-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                        </span>
                    </div>
                    :
                    <input  type="number" min={0} class={`input-field input-field-edit ${ (row.variation_stock_quantity>0) ? "value-sucess" : "value-danger"}`}  value={ (row.variation_stock_quantity === null) ? '0' : row.variation_stock_quantity } readOnly/> 
                }
            </div>
            <div className="cell div-subcriberno">{row.product_interested_person}</div>
        </div>
    </div>
  );
};

export default TableRow;