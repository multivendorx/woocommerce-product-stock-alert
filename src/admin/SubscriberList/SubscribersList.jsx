import axios from 'axios';
import { CSVLink } from 'react-csv';
import { __ } from '@wordpress/i18n';
import { DateRangePicker } from 'rsuite';
import Dialog from "@mui/material/Dialog";
import React, { useState, useEffect } from 'react';
import Popoup from '../PopupContent/PopupContent';
import CustomTable from '../CustomLibrary/CustomTable/CustomTable';
export default function SubscribersList() {

    const fetchSubscribersDataUrl = `${ stockManagerAppLocalizer.apiUrl }/stockmanager/v1/get-subscriber-list`;
    const [ post_status , setPost_status ] = useState( 'any' );
    const [ data, setData ] = useState([]);
    const [ totalRows , setTotalRows ] = useState();
    const [ openDialog, setOpenDialog ] = useState ( false );
    const [ subscribersStatus, setSubscribersStatus ] = useState( {
        totalSubscribers: 0 ,
        subscribed: 0,
        unsubscribed: 0,
        mailSent: 0
    } )
    const currentDate = new Date();
    const sevenDaysAgo = new Date();
    sevenDaysAgo.setDate( currentDate.getDate() - 7 );
    
    function requestData( rowsPerPage = 10, currentPage = 1, productNameField = '' , emailField = '', start_date = sevenDaysAgo, end_date = currentDate ) {
        //Fetch the data to show in the table
        axios({
            method: "post",
            url: fetchSubscribersDataUrl,
            headers: { 'X-WP-Nonce' : stockManagerAppLocalizer.nonce },
            data: { page: currentPage , row: rowsPerPage, post_status:post_status
                ,product_name: productNameField, email: emailField, start_date: start_date
                ,end_date: end_date },
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

    const requestApiForData = ( rowsPerPage, currentPage, filterData = {} ) => {
        requestData( rowsPerPage, currentPage, filterData?.productNameField, filterData?.emailField, filterData?.date?.start_date, filterData?.date?.end_date )        
    }

    const realtimeFilter = [
        {
          name: "productNameField",
          render: (updateFilter, filterValue) => (
                <>
                    <div className="woo-header-search-section">
                        <input
                        name="productNameField"
                        type="text"
                        placeholder={ __( 'Search by Product Name', 'woocommerce-stock-manager' ) }
                        onChange={(e) => updateFilter(e.target.name, e.target.value)}
                        value={filterValue}
                        />
                    </div>
                </>
            ),
        },
        {
          name: "emailField",
          render: (updateFilter, filterValue) => (
                <>
                    <div className="woo-header-search-section">
                        <input
                        name="emailField"
                        type="text"
                        placeholder={ __( 'Search by Email', 'woocommerce-stock-manager' ) }
                        onChange={(e) => updateFilter(e.target.name, e.target.value)}
                        value={filterValue}
                        />
                    </div>
                </>
            ),
        },
        {
            name: "date",
            render: (updateFilter, value) => (
              <>
                <DateRangePicker placeholder={ __( 'DD-MM-YYYY ~ DD-MM-YYYY', 'woocommerce-stock-manager' ) }
                    onChange={(dates) => {
                        if ( dates != null ) {
                            updateFilter( "date",{
                                start_date : dates[0].toString().replace(/ GMT[+-]\d{4} \(.+$/, ''),
                                end_date   : dates[1].toString().replace(/ GMT[+-]\d{4} \(.+$/, '')
                            })
                        } else {
                            updateFilter( "date",{
                                start_date : sevenDaysAgo,
                                end_date   : currentDate
                            })
                        }
                    } }
                />
              </>
            ),
          }
      ];

    useEffect( () => {
        if( stockManagerAppLocalizer.pro_active != 'free' ) {
            requestData();
        }
    }, [post_status] );
    
    //columns for the data table
    const columns = [
        {
            name: __( "Date","woocommerce-stock-manager" ),
            selector: row => row.date,
        },
        {
            name: __( "Email","woocommerce-stock-manager" ),
            selector: row => row.email,
        },
        {
            name: __( "Product","woocommerce-stock-manager" ),
            selector: row => row.product,
        },
        {
            name: __( "Registered","woocommerce-stock-manager" ),
            selector: row => row.reg_user,
        },
        {
            name: __( "Status","woocommerce-stock-manager" ),
            selector: row => row.status,
        }
    ];
    return (
        <div>
            { stockManagerAppLocalizer.pro_active == 'free'  ?
                <div>
                    <Dialog
                        className="woo-module-popup"
                        open={ openDialog }
                        onClose={ () => { setOpenDialog( false ) } }
                        aria-labelledby="form-dialog-title"
                    >
                        <span 
                            className="icon-cross stock-manager-popup-cross"
                            onClick={ () => { setOpenDialog ( false ) } }
                        ></span>
                        <Popoup/>
                    </Dialog>
                    <img
                        src={ stockManagerAppLocalizer.subscriber_list }
                        alt="subscriber-list"
                        className='subscriber-img'
                        onClick={ () => { setOpenDialog ( true ) } }
                    />
                </div>
            :
                <div className="woo-subscriber-list">
                    <div className="woo-container">
                            <div className="woo-middle-container-wrapper">
                                <div className="woo-page-title">
                                    <p>{__("Subscriber List","woocommerce-stock-manager")}</p>
                                    <div className="download-btn-subscriber-list">
                                        <CSVLink
                                            data={ data }
                                            headers={ stockManagerAppLocalizer.columns_subscriber_list }
                                            filename={ 'Subscribers.csv' }
                                            className="woo-btn btn-purple"
                                        >
                                            <i className="woo-font icon-download"></i>
                                            { __( 'Download CSV', 'woocommerce-stock-manager' ) }
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
                                <div className="woo-backend-datatable-wrapper">
                                    {
                                        <CustomTable 
                                            data={data}
                                            columns={columns}
                                            handlePagination={requestApiForData}
                                            defaultRowsParPage={10}
                                            defaultTotalRows={totalRows}
                                            perPageOption={[10, 25, 50]}
                                            realtimeFilter={realtimeFilter}
                                        />
                                    }
                                </div>                       
                            </div>
                        </div>
                </div>
            }
        </div>
    );
}
