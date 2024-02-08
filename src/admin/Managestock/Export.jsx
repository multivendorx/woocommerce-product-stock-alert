import React, { useState } from 'react';
import { CSVLink } from "react-csv";
import { __ } from '@wordpress/i18n';

const Export = ({data}) =>{
    const [header,setHeader] = useState([]);
    const handleCheck = (e,label,key) => {
        let str = `{"label":"${label}","key":"${key}"}`;
        str = JSON.parse(str);
        if(e.target.checked){
            setHeader(prevHeader => [...prevHeader, str]);
        }else{
            setHeader(prevHeader => prevHeader.filter(header => header.label !== str.label || header.key !== str.key));
        }
    };
    return(
        <div className="woo-container">
            <div className="woo-page-title">
                <p>{__('Import/Export','woocommerce-stock-manager-pro')}</p>
            </div>
            <div className="export-section">
                <h2>{__('Export','woocommerce-stock-manager-pro')}</h2>
                <br />
                <p>{__('You can download csv file,with stock data.','woocommerce-stock-manager-pro')}<br />{__('Please Select the field of which you want to download the csv.','woocommerce-stock-manager-pro')}</p>
                <div>
                    <input type="checkbox" id="Id" onChange={(e)=>{handleCheck(e,"Id","product_id")}} />
                    <label htmlFor="Id">{__('Id','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="Type" onChange={(e)=>{handleCheck(e,"Type","product_type")}} />
                    <label htmlFor="Type">{__('Type','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="SKU" onChange={(e)=>{handleCheck(e,"SKU","product_sku")}} />
                    <label htmlFor="SKU">{__('SKU','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="Name" onChange={(e)=>{handleCheck(e,"Name","product_name")}} />
                    <label htmlFor="Name">{__('Name','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="Manage Stock" onChange={(e)=>{handleCheck(e,"Manage Stock","product_manage_stock")}} />
                    <label htmlFor="Manage Stock">{__('Manage Stock','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="Stock status" onChange={(e)=>{handleCheck(e,"Stock status","product_stock_status")}} />
                    <label htmlFor="Stock status">{__('Stock status','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="Backorders" onChange={(e)=>{handleCheck(e,"Backorders","product_backorders")}} />
                    <label htmlFor="Backorders">{__('Backorders','woocommerce-stock-manager-pro')}</label>
                </div>
                <div>
                    <input type="checkbox" id="Stock Quantity" onChange={(e)=>{handleCheck(e,"Stock Quantity","product_stock_quantity")}} />
                    <label htmlFor="Stock Quantity">{__('Stock Quantity','woocommerce-stock-manager-pro')}</label>  
                </div>              
                <button class="import-export-btn">
                    <CSVLink enclosingCharacter={``} data={data} headers={header} filename={'Products.csv'}>{__('Export CSV','woocommerce-stock-manager-pro')}</CSVLink>
                </button>
            </div>
        </div>
    )
}
export default Export;