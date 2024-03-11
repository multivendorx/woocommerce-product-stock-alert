import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { CSVLink } from "react-csv";
import { __ } from '@wordpress/i18n';
import { Link } from 'react-router-dom';

const Export = () => {
    //Headers to generate the CSV
    const headers = [
        { label: 'Id', key: 'product_id' },
        { label: 'Type', key: 'product_type' },
        { label: 'SKU', key: 'product_sku' },
        { label: 'Name', key: 'product_name' },
        { label: 'Manage stock', key: 'product_manage_stock' },
        { label: 'Stock status', key: 'product_stock_status' },
        { label: 'Backorders', key: 'product_backorders' },
        { label: 'Stock', key: 'product_stock_quantity' },
    ];
    useEffect( ( ) => {
        if (stockManagerAppLocalizer.pro_active != 'free') {
            axios({
                method: "post",
                url: `${ stockManagerAppLocalizer.apiUrl }/stockmanager/v1/get-products`,
                data:{ allData: 'true' },
            }).then( ( response ) => {
                let parsedData = JSON.parse( response.data );
                setData( parsedData );
            });
        }
    }, []);
    const [ data, setData ] = useState([ ]);
    const [ header, setHeader ] = useState( headers );
    const [ selectAll, setSelectAll ] = useState( true );
    //Data to Generate the Checkbox
    const [ checkboxData, setCheckboxData ] = useState([
        { Name: 'Id',           Value: 'product_id',             Checked: true },
        { Name: 'Type',         Value: 'product_type',           Checked: true },
        { Name: 'SKU',          Value: 'product_sku',            Checked: true },
        { Name: 'Name',         Value: 'product_name',           Checked: true },
        { Name: 'Manage stock', Value: 'product_manage_stock',   Checked: true },
        { Name: 'Stock status', Value: 'product_stock_status',   Checked: true },
        { Name: 'Backorders',   Value: 'product_backorders',     Checked: true },
        { Name: 'Stock',        Value: 'product_stock_quantity', Checked: true }
    ]);

    const handleCheck = ( e, label, key ) => {
        setCheckboxData( ( prevCheckboxData ) =>
            prevCheckboxData.map( ( checkbox ) =>
                checkbox.Name === label
                    ? { ...checkbox, Checked: !checkbox.Checked }
                    : checkbox
            )
        );
        let str = `{"label":"${ label }","key":"${ key }"}`;
        str = JSON.parse( str );
        if ( e.target.checked ) {
            setHeader( prevHeader => [ ...prevHeader, str ] );
        } else {
            setHeader( prevHeader => prevHeader.filter( header => header.label !== str.label || header.key !== str.key ) );
        }
    };

    const handleSelectAll = ( ) => {
        if ( !selectAll ) {
            setCheckboxData( checkboxData.map( item => ( { ...item, Checked: true } ) ) );
            setHeader( headers);
            setSelectAll( true);
        } else {
            setCheckboxData( checkboxData.map( item => ( { ...item, Checked: false } ) ) );
            setHeader([ ]);
            setSelectAll( false );
        }
    };
    function splitCheckBoxData( parts ) {
        const chunks = [ ];
        for ( let i = 0; i < checkboxData.length; i += parts ) {
            const chunk = checkboxData.slice( i, i + parts );
            const chunkElements = chunk.map( ( checkbox ) => (
                <div className='export-feature-card' key={ checkbox.Value }>
                    <h1>{ checkbox.Name }</h1>
                    <div className="mvx-normal-checkbox-content">
                        <input
                            type="checkbox"
                            id={ checkbox.Name }
                            checked={ checkbox.Checked }
                            onChange={ ( e ) => handleCheck( e, checkbox.Name, checkbox.Value ) }
                        />
                    </div>
                </div>
            ));
            chunks.push(
                <div key={ i } className="chunk-container">
                    { chunkElements }
                </div>
            );
        }
        return chunks;
    }
    return (
        <div className="woo-container">
            <div className='export-page'>
                <div className="woo-page-title">
                    <p>{ __( 'Export', 'woocommerce-stock-manager-pro' ) }</p>
                    <button class="import-export-btn" >
                        <Link to={ '?page=woo-stock-manager-setting#&tab=manage-stock' }>
                            <div className='wp-menu-image dashicons-before dashicons-arrow-left-alt'></div>
                            { __( "Inventory Manager", "woocommerce-stock-manager-pro" ) }
                        </Link>
                    </button>
                </div>
                <div className="export-section">
                    <p>{ __( 'Download a CSV file containing stock data. Choose specific fields for CSV download.', 'woocommerce-stock-manager-pro' ) }</p>
                    <div className='export-page-content'>
                        <div className='import-export-btn-section'>
                            <div>
                                <button class="mvx-select-deselect-trigger" onClick={ handleSelectAll } >{ __( 'Select / Deselect All', 'woocommerce-stock-manager-pro' ) }</button>
                            </div>
                        </div>
                        <div className="export-list-section">
                            <p>{ __( 'Select fields for exports', 'woocommerce-stock-manager-pro' ) }</p>
                            <div className='checkbox-container'>
                                { splitCheckBoxData( 4 ) }
                            </div>
                        </div>
                        <button class="import-export-btn">
                            <div className='wp-menu-image dashicons-before dashicons-upload'></div>
                            {
                                data &&
                                <CSVLink enclosingCharacter={``} data={ Object.values( data ) } headers={ header } filename={ 'Products.csv'} >{ __( 'Export CSV', 'woocommerce-stock-manager-pro' ) }</CSVLink>
                            }
                        </button>
                    </div>
                </div>
            </div>
        </div>
    )
}
export default Export;