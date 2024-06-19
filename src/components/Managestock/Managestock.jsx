import axios from "axios";
import { __ } from "@wordpress/i18n";
import Dialog from "@mui/material/Dialog";
import React, { useEffect, useState } from "react";
import Popoup from "../PopupContent/PopupContent";
import { Link } from "react-router-dom";
import ProductTable from "./ManagestockComponents/ProductTable";
import "./Managestock.scss";
import { useRef } from "react";
const Managestock = () => {
  // Loading table component.
  const LoadingTable = () => {
    // Array to represent 10 rows
    const rows = Array.from({ length: 10 }, (_, index) => index);
    return (
      <>
        <table className="load-table">
          <tbody>
            {/* Loop to render 10 table rows */}
            {rows.map((row, rowIndex) => (
              <tr key={rowIndex}>
                {/* Loop to render 8 cells in each row */}
                {Array.from({ length: 5 }, (_, cellIndex) => (
                  <td key={cellIndex} className="load-table-td">
                    <div className="line" />
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </>
    );
  };
  const fetchDataUrl   = `${appLocalizer.apiUrl}/stockmanager/v1/get-products`;
  const segmentDataUrl = `${appLocalizer.apiUrl}/stockmanager/v1/all-products`;
  const [data, setData] = useState(null);
  const [headers, setHeaders] = useState([]);
  const [totalProducts, setTotalProducts] = useState();
  const [rowsPerPage, setRowsPerPage] = useState(10);
  const [currentPage, setCurrentPage] = useState(0);
  const [displayMessage, setDisplayMessage] = useState("");
  const [openDialog, setOpenDialog] = useState(false);
  const [searchValue, setSearchValue] = useState("");
  const [searchType, setSearchType] = useState("");
  const [productType, setProductType] = useState("");
  const [stockStatus, setStockStatus] = useState("");
  const [segments, setSegments] = useState(null);
  const filterChanged = useRef(false);
  useEffect(() => {
    if (!appLocalizer.pro_active) return;
    axios({
      method: "post",
      url: segmentDataUrl,
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: { segment: true },
    }).then((response) => {
      setSegments(response.data);
    });
  }, []);
  useEffect(() => {
    if (!appLocalizer.pro_active) return;
    if (filterChanged.current && (Boolean(searchType) ^ Boolean(searchValue))) {
      filterChanged.current = false;
      return;
    }
    setData(null);
    //Fetch the data to show in the table
    axios({
      method: "post",
      url: fetchDataUrl,
      headers: { "X-WP-Nonce": appLocalizer.nonce },
      data: {
        page: currentPage + 1,
        row: rowsPerPage,
        product_name: searchType == 'productName' ? searchValue: null,
        product_sku: searchType == 'productSku' ? searchValue: null,
        product_type: productType,
        stock_status: stockStatus,
      },
    }).then((response) => {
      let parsedData = JSON.parse(response.data);
      setData(parsedData.products);
      setHeaders(parsedData.headers);
      setTotalProducts(parsedData.total_products);
    });
  }, [
    rowsPerPage,
    currentPage,
    searchValue,
    searchType,
    productType,
    stockStatus,
  ]);
  return (
    <>
      {!appLocalizer.pro_active ? (
        //If the user is free user he will be shown a Inventory Manager image
        <div className="inventory-manager-wrapper">
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
            onClick={() => {
              setOpenDialog(true);
            }}
            className="inventory-manager"
          ></div>
        </div>
      ) : (
        //If user is pro user he will shown the Inventory Manager Table
        <div className="admin-middle-container-wrapper">
          <div className="title-section">
            <p>{__("Inventory Manager", "woocommerce-stock-manager")}</p>
            <div className="stock-reports-download">
              <button class="import-export-btn">
                <Link to={"?page=stock-manager#&tab=import"}>
                  <div className="wp-menu-image dashicons-before dashicons-download"></div>
                  {__("Import", "woocommerce-stock-manager")}
                </Link>
              </button>
              <button class="import-export-btn">
                <Link to={"?page=stock-manager#&tab=export"}>
                  <div className="wp-menu-image dashicons-before dashicons-upload"></div>
                  {__("Export", "woocommerce-stock-manager")}
                </Link>
              </button>
            </div>
            {displayMessage && (
              <div className="admin-notice-display-title">
                <i className="admin-font font-icon-yes"></i>
                {displayMessage}
              </div>
            )}
          </div>
          {/* Table segments */}
          <div className="admin-table-wrapper-filter">
            <div className={stockStatus === '' ? 'type-count-active' : ''} onClick={(e) => {
              setStockStatus('');
            }}>
              All ({segments?.all || 0})
            </div>
            <div className={stockStatus === 'instock' ? 'type-count-active' : ''} onClick={(e) => {
              setStockStatus('instock');
            }}>
              In stock ({segments?.instock || 0})
            </div>
            <div className={stockStatus === 'onbackorder' ? 'type-count-active' : ''} onClick={(e) => {
              setStockStatus('onbackorder');
            }}>
              On backorder ({segments?.onbackorder || 0})
            </div>
            <div className={stockStatus === 'outofstock' ? 'type-count-active' : ''} onClick={(e) => {
              setStockStatus('outofstock');
            }}>
              Out of stock ({segments?.outofstock || 0})
            </div>
          </div>
          <div className="manage-stock-wrapper">
            <div class="admin-wrap-bulk-all-date">
              <div class="custom-select">
                <select
                  onChange={(e) => {
                    setProductType(e.target.value);
                  }}
                >
                  <option value="">
                    {__("Product Type", "woocommerce-stock-manager")}
                  </option>
                  <option value="Simple">
                    {__("Simple", "woocommerce-stock-manager")}
                  </option>
                  <option value="Variable">
                    {__("Variable", "woocommerce-stock-manager")}
                  </option>
                </select>
              </div>
              <div class="admin-header-search-section product-search">
                  <input
                    type="text"
                    placeholder="Search..."
                    onChange={(e) => {
                      e.preventDefault();
                      filterChanged.current = true;
                      setSearchValue(e.target.value);
                    }}
                />
              </div>
              <div class="admin-header-search-section">
                <select
                  name="searchAction"
                  onChange={(e) => {
                    e.preventDefault();
                    filterChanged.current = true;
                    setSearchType(e.target.value);
                  }}
                  value={searchType}
                >
                  {/* <option value="">All</option> */}
                  <option value="">--Select--</option>
                  <option value="productName">Product Name</option>
                  <option value="productSku">Sku</option>
                </select>
              </div>
            </div>
          </div>
          {
            //If both the data nad the headers are set then only the Table will be shown else the <PuffLoader/> will be shown
            data &&
              Object.keys(headers).length > 0 ? (
              <div className="manage-stock-table">
                <ProductTable
                  setData={setData}
                  setDisplayMessage={setDisplayMessage}
                  totalProducts={totalProducts}
                  rowsPerPage={rowsPerPage}
                  setRowsPerPage={setRowsPerPage}
                  currentPage={currentPage}
                  setCurrentPage={setCurrentPage}
                  headers={headers}
                  products={data}
                />
              </div>
            ) : (
              <LoadingTable />
            )
          }
        </div>
      )}
    </>
  );
};
export default Managestock;