import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { CSVLink } from "react-csv";
import { __ } from '@wordpress/i18n';
import { Link } from 'react-router-dom';
import "./importExport.scss";

const Export = () => {
    const [ data, setData ] = useState([]);
    const [ selectAll, setSelectAll ] = useState( true );
    
    //Fetches the data for generating the csv
    useEffect( () => {
        if (appLocalizer.pro_active) {
            axios({
                method: "post",
                url: `${ appLocalizer.apiUrl }/stockmanager/v1/all-products`,
                headers: { 'X-WP-Nonce' : appLocalizer.nonce },
            }).then((response) => {
                let parsedData = JSON.parse(response.data);
                setData( Object.values(parsedData) );
            });
        }
    }, []);

    //Data to Generate the Checkbox
    const [ checkboxData, setCheckboxData ] = useState([
        { name: 'Id',           value: 'id',             checked: true },
        { name: 'Type',         value: 'type',           checked: true },
        { name: 'SKU',          value: 'sku',            checked: true },
        { name: 'name',         value: 'name',           checked: true },
        { name: 'Manage stock', value: 'manage_stock',   checked: true },
        { name: 'Stock status', value: 'stock_status',   checked: true },
        { name: 'Backorders',   value: 'backorders',     checked: true },
        { name: 'Stock',        value: 'stock_quantity', checked: true },
    ]);

    const getHeader = () => {
        const header = [];
        checkboxData.forEach(({ name, value, checked }) => {
            if (checked) {
                header.push({ label: name, key: value });
            }
        });
        return header;
    }

    const getData = () => {
        return data.map((row) => {
            return {
                ...row,
                manage_stock: row.manage_stock ? 'yes' : 'no',
                stock_quantity: row.stock_quantity || '-',
            }
        });
    }

    //Handles the selection of a csv field
    const handleCheck = (e, name, value) => {
        console.log(name, value);
        setCheckboxData( ( prevCheckboxData ) =>
            prevCheckboxData.map( ( checkbox ) =>
                checkbox.value === value
                    ? { ...checkbox, checked: !checkbox.checked }
                    : checkbox
            )
        );
    };

    const handleSelectAll = () => {
        if ( !selectAll ) {
            setCheckboxData( checkboxData.map( item => ( { ...item, checked: true } ) ) );
            setSelectAll( true);
        } else {
            setCheckboxData( checkboxData.map( item => ( { ...item, checked: false } ) ) );
            setSelectAll( false );
        }
    };

    //splits the checkbox data in parts
    function splitCheckBoxData( parts ) {
        const chunks = [];
        for ( let i = 0; i < checkboxData.length; i += parts ) {
            const chunk = checkboxData.slice( i, i + parts );
            const chunkElements = chunk.map( ( checkbox ) => (
                <div className='export-feature-card' key={ checkbox.value }>
                    <h1>{ checkbox.name }</h1>
                    <div className="mvx-normal-checkbox-content">
                        <input
                            type="checkbox"
                            id={ checkbox.name }
                            checked={ checkbox.checked }
                            onChange={ ( e ) => handleCheck( e, checkbox.name, checkbox.value ) }
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
        <div className="admin-container">
            <div className='export-page'>
                <div className="admin-page-title">
                    <p>{ __( 'Export', 'woocommerce-stock-manager' ) }</p>
                    <button class="import-export-btn" >
                        <Link to={ '?page=stock-manager#&tab=manage-stock' }>
                            <div className='wp-menu-image dashicons-before dashicons-arrow-left-alt'></div>
                            { __( "Inventory Manager", "woocommerce-stock-manager" ) }
                        </Link>
                    </button>
                </div>
                <div className="export-section">
                    <p>{ __( 'Download a CSV file containing stock data. Choose specific fields for CSV download.', 'woocommerce-stock-manager' ) }</p>
                    <div className='export-page-content'>
                        <div className='import-export-btn-section'>
                            <p>{ __( 'Select fields for exports', 'woocommerce-stock-manager' ) }</p>
                            <div>
                                <button class="select-deselect-trigger" onClick={ handleSelectAll } >{ __( 'Select / Deselect All', 'woocommerce-stock-manager' ) }</button>
                            </div>
                        </div>
                        <div className="export-list-section">
                            <div className='checkbox-container'>
                                { splitCheckBoxData( 4 ) }
                            </div>
                        </div>
                        <button class="import-export-btn">
                            <div className='wp-menu-image dashicons-before dashicons-upload'></div>
                            {
                                data &&
                                <CSVLink enclosingCharacter={``} data={ getData(data) } headers={ getHeader() } filename={ 'Products.csv'} >{ __( 'Export CSV', 'woocommerce-stock-manager' ) }</CSVLink>
                            }
                        </button>
                    </div>
                </div>
            </div>
        </div>
    )
}
export default Export;