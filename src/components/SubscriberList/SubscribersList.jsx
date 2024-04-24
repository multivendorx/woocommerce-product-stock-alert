import axios from "axios";
import { CSVLink } from "react-csv";
import { __ } from "@wordpress/i18n";
import { DateRangePicker } from "rsuite";
import Dialog from "@mui/material/Dialog";
import React, { useState, useEffect, useMemo } from "react";
import Popoup from "../PopupContent/PopupContent";
import CustomTable, {
  TableCell,
} from "../AdminLibrary/CustomTable/CustomTable";
import "./subscribersList.scss";
import "./rsuite-default.min.css";
import isAfter from 'date-fns/isAfter';

export default function SubscribersList() {
  const fetchSubscribersDataUrl = `${appLocalizer.apiUrl}/stockmanager/v1/get-subscriber-list`;
  const fetchSubscribersCount = `${appLocalizer.apiUrl}/stockmanager/v1/get-table-segment`;
  const [postStatus, setPostStatus] = useState("");
  const [data, setData] = useState(null);
  const [selectedRows, setSelectedRows] = useState([]);
  const [totalRows, setTotalRows] = useState();
  const [openDialog, setOpenDialog] = useState(false);
  const [subscribersStatus, setSubscribersStatus] = useState(null);

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
        <>
          <DateRangePicker
            placeholder={__(
              "DD-MM-YYYY ~ DD-MM-YYYY",
              "woocommerce-stock-manager"
            )}
            shouldDisableDate={date => isAfter(date, new Date())}
            onChange={(dates) => {
              if (dates != null) {
                updateFilter("date", {
                  start_date: dates[0]
                    .toString()
                    .replace(/ GMT[+-]\d{4} \(.+$/, ""),
                  end_date: dates[1]
                    .toString()
                    .replace(/ GMT[+-]\d{4} \(.+$/, ""),
                });
              } else {
                updateFilter("date", {
                  start_date: sevenDaysAgo,
                  end_date: currentDate,
                });
              }
            }}
          />
        </>
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
          <img
            src={appLocalizer.subscriber_list}
            alt="subscriber-list"
            className="subscriber-img"
            onClick={() => {
              setOpenDialog(true);
            }}
          />
        </div>
      ) : (
        <div className="admin-subscriber-list">
          <div className="admin-container">
            <div className="subscriber-container-wrapper">
              <div className="admin-page-title">
                <p>{__("Subscriber List", "woocommerce-stock-manager")}</p>
                <div className="download-btn-subscriber-list">
                  <CSVLink
                    data={filterForCSV(data || [])}
                    headers={appLocalizer.columns_subscriber_list}
                    filename={"Subscribers.csv"}
                    className="admin-btn btn-purple"
                  >
                    <i className="admin-font icon-download"></i>
                    {__("Download CSV", "woocommerce-stock-manager")}
                  </CSVLink>
                </div>
              </div>

                <div className="admin-table-wrapper">
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
                  />
                }
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
