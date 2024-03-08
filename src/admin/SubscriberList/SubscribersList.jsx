import axios from 'axios';
import { CSVLink } from 'react-csv';
import { css } from '@emotion/react';
import { __ } from '@wordpress/i18n';
import { DateRangePicker } from 'rsuite';
import Dialog from "@mui/material/Dialog";
import ReactPaginate from "react-paginate";
import React, { useState, useEffect } from 'react';
import DataTable from 'react-data-table-component';
import PuffLoader from 'react-spinners/PuffLoader';
import Popoup from '../PopupContent/PopupContent';

export default function SubscribersList( ) {
    const fetchSubscribersDataUrl = `${ stockManagerAppLocalizer.apiUrl }/stockmanager/v1/get-subscriber-list`;
    const [ rowsPerPage , setRowsPerPage ] = useState( 10 );
    const [ currentPage , setCurrentPage ] = useState( 0 );
    const [ post_status , setPost_status] = useState( 'any' );
    const [ productNameField , setProductNameField ] = useState( '' );
    const [ emailField , setEmailField ] = useState( '' );
    const [ data, setData ] = useState([ ]);
    const [ totalRows , setTotalRows ] = useState( );
    const [ subscribersStatus, setSubscribersStatus ] = useState( {
        totalSubscribers: 0 ,
        subscribed: 0,
        unsubscribed: 0,
        mailSent: 0
    } )
    const currentDate = new Date( );
    const sevenDaysAgo = new Date( );
    sevenDaysAgo.setDate( currentDate.getDate( ) - 7 );
    const [ date , setDate ] = useState( {
        start_date: sevenDaysAgo,
        end_date: currentDate
    } )

    const debounce = (func, delay) => {
        let timer;
        return (...args) => {
          clearTimeout(timer);
          timer = setTimeout(() => {
            func(...args);
          }, delay);
        };
    };

    useEffect(() => {
        const delayedSearch = debounce(performSearch, 500);
        delayedSearch(productNameField);
    }, [productNameField]);
    
    //   const handleInputChange = (event) => {
    //     setSearchQuery(event.target.value);
    //   };

    const override = css`
        display: block;
        margin: 0 auto;
        border-color: red;
    `;
    useEffect( ( ) => {
        if( stockManagerAppLocalizer.pro_active != 'free' ) {
            //Fetch the data to show in the table   
            axios({
                method: "post",
                url: fetchSubscribersDataUrl,
                data: { page: currentPage + 1, row: rowsPerPage, post_status:post_status
                    ,product_name: productNameField, email: emailField, start_date: date.start_date
                    ,end_date: date.end_date },
            }).then((response) => {
                let parsedData = JSON.parse ( response.data );
                let subscribe_count = parsedData.subscribe_count;
                setData  (parsedData.subscribe_list );
                setTotalRows ( subscribe_count [ post_status ] );
                setSubscribersStatus ({
                    totalSubscribers: subscribe_count.any ,
                    subscribed:       subscribe_count.woo_subscribed,
                    unsubscribed:     subscribe_count.woo_unsubscribed,
                    mailSent:         subscribe_count.woo_mailsent
                })
            });
        }
        //Data to be loaded for the changes of the following states
    }, [ rowsPerPage, currentPage, post_status, productNameField, emailField, date ] );
    const [ openDialog, setOpenDialog ] = useState ( false );
    
    const handleDateRangeChange = ( dates ) => {
        if ( dates != null ) {
            setDate({
                start_date : dates[0].toString().replace(/ GMT[+-]\d{4} \(.+$/, ''),
                end_date   : dates[1].toString().replace(/ GMT[+-]\d{4} \(.+$/, '')
            })
        }
    };
    //columns for the data table
    const columns = [
        {
            name: __( "Date","woocommerce-stock-manager-pro" ),
            selector: row => row.date,
        },
        {
            name: __( "Email","woocommerce-stock-manager-pro" ),
            selector: row => row.email,
        },
        {
            name: __( "Product","woocommerce-stock-manager-pro" ),
            selector: row => row.product,
        },
        {
            name: __( "Registered","woocommerce-stock-manager-pro" ),
            selector: row => row.reg_user,
        },
        {
            name: __( "Status","woocommerce-stock-manager-pro" ),
            selector: row => row.status,
        }
    ];
    //Pagination component
    const Pagination = ( ) => {
        const handlePageChange = ( { selected } ) => {
            setCurrentPage ( selected );
            window.scrollTo ( {
            top: 0,
            behavior: 'smooth',
            } );
        };
        const handleRowsPerPageChange = ( e ) => {
            setRowsPerPage( parseInt ( e.target.value ) );
            window.scrollTo({
            top: 0,
            behavior: 'smooth',
            });
            setCurrentPage( 0 );
        };
        return(
            <div className="pagination">
                <div>
                    <label htmlFor="rowsPerPage" > { __( "Rows per page:","woocommerce-stock-manager-pro" ) } </label>
                    <select id="rowsPerPage" value={ rowsPerPage } onChange={ handleRowsPerPageChange } >
                        {
                            [10,25,30,50].map( ( value ) => {
                            return <option value={value}> {value} </option>
                            })
                        }
                        <option value={ totalRows }>{__( "All", "woocommerce-stock-manager-pro" ) }</option>
                    </select>          
                </div>
                <ReactPaginate
                className="pagination"
                previousLabel={"previous"}
                nextLabel={"next"}
                breakLabel={"..."}
                breakClassName={"break-me"}
                pageCount={ totalRows ? Math.ceil ( totalRows / rowsPerPage ) : 0 }
                marginPagesDisplayed={ 2 }
                pageRangeDisplayed={ 2 }
                onPageChange={ handlePageChange }
                />
            </div>
        )
    }
    return (
        <div>
            { stockManagerAppLocalizer.pro_active == 'free'  ?
                <div>
                    <Dialog
                        className="woo-module-popup"
                        open={ openDialog }
                        onClose={ ( ) => { setOpenDialog( false ) } }
                        aria-labelledby="form-dialog-title"
                    >
                        <span 
                            className="icon-cross stock-manager-popup-cross"
                            onClick={ ( ) => { setOpenDialog ( false ) } }
                        ></span>
                        <Popoup/>
                    </Dialog>
                    <img
                        src={ stockManagerAppLocalizer.subscriber_list }
                        alt="subscriber-list"
                        className='subscriber-img'
                        onClick={ ( ) => { setOpenDialog ( true ) } }
                    />
                </div>
            :
                <div className="woo-subscriber-list">
                    <div className="woo-container">
                            <div className="woo-middle-container-wrapper">
                                <div className="woo-page-title">
                                    <p>{__("Subscriber List","woocommerce-stock-manager-pro")}</p>
                                    <div className="download-btn-subscriber-list">
                                        <CSVLink
                                            data={ data }
                                            headers={ stockManagerAppLocalizer.columns_subscriber_list }
                                            filename={ 'Subscribers.csv' }
                                            className="woo-btn btn-purple"
                                        >
                                            <i className="woo-font icon-download"></i>
                                            { stockManagerAppLocalizer.download_csv }
                                        </CSVLink>
                                    </div>
                                </div>
                                <div className="woo-search-and-multistatus-wrap">
                                    <ul className="woo-multistatus-ul">
                                        <li className={`woo-multistatus-item ${ post_status == 'any' ? 'status-active' : '' } `}>
                                            <div onClick={ () => { setPost_status( 'any') ;setTotalRows ( subscribersStatus.totalSubscribers )  } } className="woo-multistatus-check-all ">
                                                {`All (${ subscribersStatus.totalSubscribers })`}
                                            </div>
                                        </li>
                                        <li  className="woo-multistatus-item woo-divider"></li>
                                        <li onClick={ () => { setPost_status ( 'woo_subscribed' ) ;setTotalRows ( subscribersStatus.subscribed ) } } className={`woo-multistatus-item ${ post_status == 'woo_subscribed' ? 'status-active' : '' } `}>
                                            <div className="woo-multistatus-check-subscribe">
                                                {`Subscribe (${ subscribersStatus.subscribed })`}
                                            </div>
                                        </li>
                                        <li  className="woo-multistatus-item woo-divider"></li>
                                        <li onClick={ () => { setPost_status ( 'woo_unsubscribed' ) ;setTotalRows ( subscribersStatus.unsubscribed ) } } className={`woo-multistatus-item ${ post_status == 'woo_unsubscribed' ? 'status-active' : '' } `}>
                                            <div className="woo-multistatus-check-unpaid">
                                                {`Unsubscribe (${ subscribersStatus.unsubscribed })`}
                                            </div>
                                        </li>
                                        <li className="woo-multistatus-item woo-divider"></li>
                                        <li onClick={ () => { setPost_status ( 'woo_mailsent' ) ;setTotalRows ( subscribersStatus.mailSent ) } } className={`woo-multistatus-item ${ post_status == 'woo_mailsent' ? 'status-active' : '' } `}>
                                            <div className="woo-multistatus-check-unpaid">
                                                {`Mail Sent (${ subscribersStatus.mailSent })`}
                                            </div>
                                        </li>
                                    </ul>                                    
                                </div>

                                <div className="woo-wrap-bulk-all-date">
                                    <div className="woo-header-search-section">
                                        <input type="text" placeholder={ stockManagerAppLocalizer.subscription_page_string.show_product }
                                            onChange={ ( event ) => {
                                                if ( event.target.value.length > 3 ) {
                                                    setProductNameField ( event.target.value );
                                                } else if ( event.target.value.length <= 1 ) {
                                                    setProductNameField ( '' );
                                                }
                                            }}
                                        />
                                    </div>
                                    <div className="woo-header-search-section">
                                        <label>
                                            <i className="woo-font icon-search"></i>
                                        </label>
                                        <input type="text" placeholder={ stockManagerAppLocalizer.subscription_page_string.search }
                                            onChange={ ( event ) => {
                                                if ( event.target.value.length > 3 ) {
                                                    setEmailField ( event.target.value );
                                                } else if ( event.target.value.length <= 1 ) {
                                                    setEmailField ( '' );
                                                }
                                            }}
                                        />
                                    </div>                                    
                                    <DateRangePicker placeholder={ stockManagerAppLocalizer.subscription_page_string.daterenge }
                                        onChange={ handleDateRangeChange }
                                    />
                                </div>
                                {
                                    ( data.length > 0 )?
                                        <div className="woo-backend-datatable-wrapper">
                                            <DataTable
                                                className='subscribe-list-table'
                                                columns={ columns  }
                                                data={ data }
                                                selectableRows
                                            />
                                            { Pagination() }
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
        </div>
    );
}
