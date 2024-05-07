import axios from "axios";
import { CSVLink } from "react-csv";
import { __ } from "@wordpress/i18n";
import Dialog from "@mui/material/Dialog";
import React, { useState, useEffect, useMemo, useRef } from "react";
import Popoup from "../PopupContent/PopupContent";
import CustomTable, {
  TableCell,
} from "../AdminLibrary/CustomTable/CustomTable";
import "./subscribersList.scss";

import { DateRangePicker } from 'react-date-range';
import 'react-date-range/dist/styles.css'; // main style file
import 'react-date-range/dist/theme/default.css'; // theme css file


export default function SubscribersList() {
  const fetchSubscribersDataUrl = `${appLocalizer.apiUrl}/stockmanager/v1/get-subscriber-list`;
  const fetchSubscribersCount = `${appLocalizer.apiUrl}/stockmanager/v1/get-table-segment`;
  const bulkActionUrl = `${appLocalizer.apiUrl}/stockmanager/v1/bulk-action`;
  const [postStatus, setPostStatus] = useState("");
  const [data, setData] = useState(null);
  const [selectedRows, setSelectedRows] = useState([]);
  const [totalRows, setTotalRows] = useState();
  const [openDialog, setOpenDialog] = useState(false);
  const [subscribersStatus, setSubscribersStatus] = useState(null);
  const [openDatePicker, setOpenDatePicker] = useState(false);
  const [openModal, setOpenModal] = useState(false);
  const [modalDetails, setModalDetails] = useState(false);

  const handleDateOpen = ()=>{
    setOpenDatePicker(!openDatePicker);
  }

  const [selectedRange, setSelectedRange] = useState([
    {
      startDate: new Date(),
      endDate: new Date(),
      key: 'selection'
    }
  ]);

  function requestData(
    rowsPerPage = 10,
    currentPage = 1,
    productNameField = "",
    emailField = "",
    start_date = new Date(0),
    end_date = new Date(),
    postStatus
  ) {
    //Fetch the data to show in the table
    axios({
      method: "post",
      url: fetchSubscribersDataUrl,
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: {
        page: currentPage,
        row: rowsPerPage,
        postStatus: postStatus,
        product_name: productNameField,
        email: emailField,
        start_date: start_date,
        end_date: end_date,
      },
    }).then((response) => {
      const data = JSON.parse(response.data);
      setData(data);
    });
  }

  const requestApiForData = (rowsPerPage, currentPage, filterData = {}) => {
    requestData(
      rowsPerPage,
      currentPage,
      filterData?.productNameField,
      filterData?.emailField,
      filterData?.date?.start_date,
      filterData?.date?.end_date,
      filterData.typeCount
    );
  };

  const filterForCSV = ( datas ) => {
    if ( selectedRows.length ) {
      datas = selectedRows;
    } 
    return datas.map(({date, product, email, status}) => { return {date, product, email, status} } );
  }



  const handleBulkAction = (event) => {
    if ( ! bulkSelectRef.current.value ) {
      setModalDetails( "Please select a action." )
      setOpenModal(true);
    }
    
    if( ! selectedRows.length ) {
      setModalDetails( "Please select products." )
      setOpenModal(true);
    }

    setData(null);

    axios({
      method: "post",
      url: bulkActionUrl,
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: {
        action: bulkSelectRef.current.value,
        subscribers: selectedRows
      },
    }).then((response) => {
      requestData();
    });
  }

  useEffect(() => {
    if (appLocalizer.pro_active) {
      requestData();
    }
  }, [postStatus]);

  useEffect(() => {
    if (appLocalizer.pro_active) {
      axios({
        method: "post",
        url: fetchSubscribersCount,
        headers: { "X-WP-Nonce": appLocalizer.nonce },
      }).then((response) => {
        response = response.data;

        setTotalRows(response["all"]);

        setSubscribersStatus([
          {
            key: "all",
            name: __("All", "woocommerce-stock-manager"),
            count: response["all"],
          },
          {
            key: "subscribed",
            name: __("Subscribed", "woocommerce-stock-manager"),
            count: response["subscribed"],
          },
          {
            key: "unsubscribed",
            name: __("Unsubscribed", "woocommerce-stock-manager"),
            count: response["unsubscribed"],
          },
          {
            key: "mailsent",
            name: __("Mail Sent", "woocommerce-stock-manager"),
            count: response["mailsent"],
          },
        ]);
      });
    }
  }, []);

  const dateRef = useRef();
  const bulkSelectRef = useRef();

  useEffect(() => {
    document.body.addEventListener("click", (event) => {
        if (! dateRef?.current?.contains(event.target) ) {
          setOpenDatePicker(false);
        }
    })
}, [])

  const realtimeFilter = [
    {
      name: "productNameField",
      render: (updateFilter, filterValue) => (
        <>
          <div className="admin-header-search-section">
            <input
              name="productNameField"
              type="text"
              placeholder={__(
                "Search by Product Name",
                "woocommerce-stock-manager"
              )}
              onChange={(e) => updateFilter(e.target.name, e.target.value)}
              value={filterValue || ""}
            />
          </div>
        </>
      ),
    },
    {
      name: "emailField",
      render: (updateFilter, filterValue) => (
        <>
          <div className="admin-header-search-section">
            <input
              name="emailField"
              type="text"
              placeholder={__("Search by Email", "woocommerce-stock-manager")}
              onChange={(e) => updateFilter(e.target.name, e.target.value)}
              value={filterValue || ""}
            />
          </div>
        </>
      ),
    },
    {
      name: "date",
      render: (updateFilter, value) => (
        <div ref={dateRef}>
          <div className="admin-header-search-section">
            <input value={`${selectedRange[0].startDate.toLocaleDateString()} - ${selectedRange[0].endDate.toLocaleDateString()}`} onClick={()=>handleDateOpen()} className="date-picker-input-custom" type="text" placeholder={__("DD/MM/YYYY", "woocommerce-stock-manager")} />
          </div>
          {openDatePicker &&
            <DateRangePicker
              ranges={selectedRange}
              months={1}
              direction="vertical"
              scroll={{ enabled: true }}
              maxDate={ new Date() }
              shouldDisableDate={date => isAfter(date, new Date())}
              onChange={(dates) => {
                if (dates.selection) {
                  dates = dates.selection;
                  dates.endDate?.setHours(23, 59, 59, 999)
                  setSelectedRange([dates])
                  updateFilter("date", {
                    start_date: dates.startDate,
                    end_date: dates.endDate,
                  });
                }
              }}
            />
          }
        </div>
      ),
    },
  ];

  //columns for the data table
  const columns = [
    {
      name: __("Product", "woocommerce-stock-manager"),
      cell: (row) => 
        <TableCell title="Product" >
          <img src={row.image} alt="product_image" />
          <p>{ row.product }</p> 
        </TableCell>,
    },
    {
      name: __("Email", "woocommerce-stock-manager"),
      cell: (row) =>
        <TableCell title="Email">
          {row.email}
          {
            row.user_link &&
            <a className="user-profile" href={ row.user_link } target="_blank"><i className="admin-font font-person"></i></a>
          }
        </TableCell>,
    },
    {
      name: __("Date", "woocommerce-stock-manager"),
      cell: (row) => <TableCell title="Date" > {row.date} </TableCell>,
    },
    {
      name: __("Status", "woocommerce-stock-manager"),
      cell: (row) => <TableCell title="Status" > 
        <p 
         className={row.status_key === 'mailsent' ? 'sent' : (row.status_key === 'subscribed' ? 'subscribed' : 'unsubscribed')}
        >{row.status}</p>
      </TableCell>,
    },
  ];

  const BulkAction = ()=>{
    console.log("Bulk Action");
    return(
      <>
        <div className="subscriber-bulk-action">
              <label>
                  { __( 'Select bulk action' ) }
              </label>
              <select name="action" ref={bulkSelectRef} >
                  <option value="">{ __( 'Bulk Actions' ) }</option>
                  <option value="unsubscribe">{ __( 'Unsubscribe Users' ) }</option>
                  <option value="delete">{ __( 'Delete Users' ) }</option>
              </select>
              <button
                  name="bulk-action-apply"
                  onClick={handleBulkAction}
              >
                  {__('Apply',)}
              </button>
          </div>

          { openModal &&
            <div className="error-modal">
              <div className="modal-wrapper">
                <div className="icons">
                  <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                  </svg>
                </div>
                <p>{modalDetails}</p>
                <button onClick={()=>setOpenModal(false)} className="button-close">Close</button>
              </div>
            </div>
          }
      </>
    )
  }

  return (
    <div>
      { ! appLocalizer.pro_active ? (
        <div>
          <Dialog
            className="admin-module-popup"
            open={openDialog}
            onClose={() => {
              setOpenDialog(false);
            }}
            aria-labelledby="form-dialog-title"
          >
            <span
              className="admin-font font-cross stock-manager-popup-cross"
              onClick={() => {
                setOpenDialog(false);
              }}
            ></span>
            <Popoup />
          </Dialog>
          <div
            className="subscriber-img"
            onClick={() => {
              setOpenDialog(true);
            }}>
          </div>
        </div>
      ) : (
        <div className="admin-subscriber-list">
          <div className="admin-page-title">
            <p>{__("Subscriber List", "woocommerce-stock-manager")}</p>
            <div className="download-btn-subscriber-list">
              <CSVLink
                data={filterForCSV(data || [])}
                headers={appLocalizer.columns_subscriber_list}
                filename={"Subscribers.csv"}
                className="admin-btn btn-purple"
              >
               <div className="wp-menu-image dashicons-before dashicons-download"></div>
                {__("Download CSV", "woocommerce-stock-manager")}
              </CSVLink>
            </div>
          </div>

            {
                <CustomTable
                data={data}
                columns={columns}
                selectable = {true}
                handleSelect = {(selectRows) => {
                  setSelectedRows(selectRows);
                }}
                handlePagination={requestApiForData}
                defaultRowsParPage={10}
                defaultTotalRows={totalRows}
                perPageOption={[10, 25, 50]}
                realtimeFilter={realtimeFilter}
                typeCounts={subscribersStatus}
                bulkActionComp = {BulkAction}
              />
            }
          </div>
      )}
    </div>
  );
}