import React, { useState } from 'react';
import TableRow from './TableRow';
import { __ } from '@wordpress/i18n';

const ProductTable = ({ products, headers, setData }) => {
  const [event, setEvent] = useState();
  const [currentPage, setCurrentPage] = useState(1);
  const [rowsPerPage, setRowsPerPage] = useState(10);

  const handlePageChange = (event, value) => {
    setCurrentPage(value);
  };

  const handleRowsPerPageChange = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setCurrentPage(1);
  };

  const PaginationRounded = () => {
    const totalPages = Math.ceil(products.length / rowsPerPage);

    return (
      <div className="pagination">
        <button
          disabled={currentPage === 1}
          onClick={() => handlePageChange(null, currentPage - 1)}
        >
          Previous
        </button>
        <span>{currentPage}</span>
        <button
          disabled={currentPage === totalPages}
          onClick={() => handlePageChange(null, currentPage + 1)}
        >
          Next
        </button>

        <span>Rows per page:</span>
        <select value={rowsPerPage} onChange={handleRowsPerPageChange}>
          <option value={5}>5</option>
          <option value={10}>10</option>
          <option value={20}>20</option>
        </select>

        <span>Total Pages: {totalPages}</span>
      </div>
    );
  };

  const renderRows = () => {
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = startIndex + rowsPerPage;
    const displayedProducts = products.slice(startIndex, endIndex);

    return (
      <React.Fragment>
        {displayedProducts.map((product) => (
          <React.Fragment key={product.product_id}>
            <TableRow
              event={event}
              setEvent={setEvent}
              key={product.product_id}
              setData={setData}
              headers={headers}
              row={product}
            />
            {product.variation && (
              <div className="expander">
                {product.variation &&
                  Object.keys(product.variation).map((variationKey) => {
                    const variation = product.variation[variationKey];
                    return (
                      <TableRow
                        event={event}
                        setEvent={setEvent}
                        key={variation.product_id}
                        setData={setData}
                        headers={headers}
                        row={variation}
                      />
                    );
                  })}
              </div>
            )}
          </React.Fragment>
        ))}
      </React.Fragment>
    );
  };

  return (
    <React.Fragment>
      <div className="custom-table-main">
        <div className="custom-container custom-container-row">
          <div className="custom-container-inner-div left">
            <div className="cell div-expand-icon">{headers.icons}</div>
            <div className="cell div-img">{headers.image}</div>
            <div className="cell div-name">{headers.name}</div>
          </div>
          <div className="custom-container-inner-div right">
            <div className="cell div-sku">{headers.sku}</div>
            <div className="cell div-product-type">{headers.type}</div>
            <div className="cell div-regular-price">{headers.regular_price}</div>
            <div className="cell div-sale-price">{headers.sale_price}</div>
            <div className="cell div-manage-stock">{headers.manage_stock}</div>
            <div className="cell div-stock-status">{headers.stock_status}</div>
            <div className="cell div-backorder">{headers.back_orders}</div>
            <div className="cell div-stock">{headers.stock}</div>
            <div className="cell div-subcriberno">{headers.subscribers}</div>
          </div>
        </div>
        {renderRows()}
      </div>
      {PaginationRounded()}
    </React.Fragment>
  );
};

export default ProductTable;
