import axios from 'axios';
import { __ } from '@wordpress/i18n';
import Dialog from "@mui/material/Dialog";
import React,{ useEffect,useState } from 'react';
import PuffLoader from 'react-spinners/PuffLoader';
import { css } from '@emotion/react';
import Popoup from '../PopupContent/PopupContent';
import { Link } from 'react-router-dom';
import ProductTable from './ProductTable';

const Managestock = () => {
    
    const fetchDataUrl = `${ stockManagerAppLocalizer.apiUrl }/stockmanager/v1/get-products`;
    
    const override = css`
    display: block;
    margin: 0 auto;
    border-color: red;
    `;
    
    const [ data, setData ] = useState([ ]);
    const [ headers, setHeaders ] = useState([]);
    const [ totalProducts , setTotalProducts ] = useState();
    const [ rowsPerPage , setRowsPerPage ] = useState(10);
    const [ currentPage , setCurrentPage ] = useState(0);
    const [ displayMessage, setDisplayMessage] = useState('');
    const [ openDialog, setOpenDialog ] = useState( false );
    const [ productName, setProductName ] = useState('');
    const [ productSku, setProductSku ] = useState('');
    const [ productType, setProductType ] = useState('');
    const [ stockStatus, setStockStatus ] = useState('');
    
    useEffect(() => {
        if( stockManagerAppLocalizer.pro_active != 'free' ) {
            //Fetch the data to show in the table
            axios( {
                method: "post",
                url: fetchDataUrl,
                data:{ page:currentPage+1 , row:rowsPerPage, product_name:productName, product_sku:productSku,
                     product_type:productType, stock_status: stockStatus, allData: 'false' },
            } ).then( ( response ) => {
                let parsedData = JSON.parse( response.data );
                setData( parsedData.products );
                setHeaders( parsedData.headers );
                setTotalProducts( parsedData.total_products );
            } );
        }
    }, [ rowsPerPage, currentPage, productName, productSku, productType, stockStatus ] );

    //Function to handle the the Name Search
    function handleInputName ( e ) {
        if ( e.target.value.length > 3 ) {
            setProductName( e.target.value )
        } else if ( e.target.value.length <= 1 ) {
            setProductName( '' );
        }
    }

    //Function to handle the SKU Search
    function handleInputSku ( e ) {
        if ( e.target.value.length > 3 ) {
            setProductSku( e.target.value )
        }else if ( e.target.value.length <= 1 ) {
            setProductSku( '' );
        }
    }
    
    return( 
    <>
        { stockManagerAppLocalizer.pro_active === 'free'  ?
        //If the user is free user he will be shown a Inventory Manager image
            <div className='subscriber-img' >
                <Dialog
                    className="woo-module-popup"
                    open={ openDialog }
                    onClose={ ( ) => { setOpenDialog ( false ) } }
                    aria-labelledby="form-dialog-title"
                >
                    <span 
                        className="icon-cross stock-manager-popup-cross"
                        onClick={ ( ) => { setOpenDialog ( false ) } }
                    ></span>
                    <Popoup/>
                </Dialog>
                <div onClick={ ( ) => { setOpenDialog ( true ) } } className='inventory-manager'></div>
            </div>
        :
        //If user is pro user he will shown the Inventory Manager Table
            <div className="woo-subscriber-list">
                    <div className="woo-container">
                        <div className="woo-middle-container-wrapper">
                            <div className="woo-search-and-multistatus-wrap">
                                <div className="woo-page-title">
                                    <p>
                                        { __( "Inventory Manager", "woocommerce-stock-manager-pro" ) }
                                    </p>
                                </div>
                            <div>
                                <div className="stock-reports-download">
                                    <div className="pull-right import">
                                        <button class="import-export-btn">
                                            <Link
                                            to={ "?page=woo-stock-manager-setting#&tab=import" }
                                            >
                                            <div className="wp-menu-image dashicons-before dashicons-download"></div>
                                            { __( "Import", "woocommerce-stock-manager-pro" ) }
                                            </Link>
                                        </button>
                                    </div>
                                    <div className="pull-right export">
                                        <button class="import-export-btn">
                                            <Link
                                            to={ "?page=woo-stock-manager-setting#&tab=export" }
                                            >
                                            <div className="wp-menu-image dashicons-before dashicons-upload"></div>
                                            { __( "Export", "woocommerce-stock-manager-pro" ) }
                                            </Link>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            { displayMessage && (
                            <div className="woo-notic-display-title">
                                <i className="icon-success-notification"></i>
                                { displayMessage }
                            </div>
                            )}
                        </div>
                        <div className="woo-search-and-multistatus-wrap">
                            <div class="woo-wrap-bulk-all-date">
                                <div class="woo-header-search-section">
                                    <input
                                    type="text"
                                    placeholder="Search by Name..."
                                    onChange={ handleInputName }
                                    />
                                </div>
                                <div class="woo-header-search-section">
                                    <input
                                    type="text"
                                    placeholder="Search by SKU..."
                                    onChange={ handleInputSku }
                                    />
                                </div>
                                <div class="custom-select">
                                    <select
                                    onChange={ ( e ) => { setProductType ( e.target.value ) } }
                                    >
                                        <option value="">
                                            { __( "Product Type", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                        <option value="Simple">
                                            { __( "Simple", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                        <option value="Variable">
                                            { __( "Variable", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                    </select>
                                </div>
                                <div class="custom-select">
                                    <select
                                    onChange={ ( e ) => { setStockStatus ( e.target.value ) } }
                                    >
                                        <option value="">
                                            { __( "Stock Status", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                        <option value="instock">
                                            { __( "In stock", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                        <option value="onbackorder">
                                            { __( "On backorder", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                        <option value="outofstock">
                                            { __( "Out of stock", "woocommerce-stock-manager-pro" ) }
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div>{__( "Results Found: ", "woocommerce-stock-manager-pro" )}{ totalProducts }</div>
                        </div>
                            {
                                //If both the data nad the headers are set then only the Table will be shown else the <PuffLoader/> will be shown
                                (( data && Object.keys( data ).length > 0 ) && ( headers && Object.keys( headers ).length > 0))?
                                    <div className="woo-backend-datatable-wrapper">
                                        <ProductTable 
                                            setData={ setData } 
                                            setDisplayMessage={ setDisplayMessage }
                                            totalProducts={ totalProducts } 
                                            rowsPerPage={ rowsPerPage } 
                                            setRowsPerPage={ setRowsPerPage }
                                            currentPage={ currentPage } 
                                            setCurrentPage={ setCurrentPage } 
                                            headers={ headers } 
                                            products={ data } 
                                        />
                                    </div>
                                :
                                    <PuffLoader
                                        css={ override }
                                        color={ '#cd0000' }
                                        size={ 200 }
                                        loading={ true }
                                    />
                            }
                        </div>
                    </div>
            </div>
        }
    </>
    );
};
export default Managestock;