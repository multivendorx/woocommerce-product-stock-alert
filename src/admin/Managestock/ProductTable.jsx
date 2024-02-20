import React, { useState } from 'react';
import TableRow from './TableRow';
import { __ } from '@wordpress/i18n';

const ProductTable = ({ products, setData }) => {
  const [event, setEvent] = useState();
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);

  const indexOfLastItem = currentPage * itemsPerPage;
  const indexOfFirstItem = indexOfLastItem - itemsPerPage;
  const currentItems = products.slice(indexOfFirstItem, indexOfLastItem);

  const renderRows = () => {
    return (
      <React.Fragment>
        {currentItems.map((product) => (
          <React.Fragment key={product.product_id}>
            <TableRow
              event={event}
              setEvent={setEvent}
              key={product.product_id}
              setData={setData}
              row={product}
            />
          </React.Fragment>
        ))}
      </React.Fragment>
    );
  };

  const paginate = (pageNumber) => setCurrentPage(pageNumber);

  const handleItemsPerPageChange = (e) => {
    setItemsPerPage(parseInt(e.target.value, 10));
    setCurrentPage(1); // Reset to the first page when changing items per page
  };

  const pageNumbers = [];
  for (let i = 1; i <= Math.ceil(products.length / itemsPerPage); i++) {
    pageNumbers.push(i);
  }

  return (
    <React.Fragment>
      <div className="pagination-controls">
        <label>
          {__('Rows per page:', 'woocommerce-stock-manager-pro')}
          <select onChange={handleItemsPerPageChange} value={itemsPerPage}>
            <option value={5}>5</option>
            <option value={10}>10</option>
            <option value={15}>15</option>
            <option value={20}>20</option>
          </select>
        </label>
      </div>
      <table className="main-table">
        <thead>
          <tr>
            <th>hello</th>
            <th><span class="dashicons img-icon dashicons-format-image"></span></th>
            <th>{__('Name','woocommerce-stock-manager-pro')}</th>
            <th>{__('SKU','woocommerce-stock-manager-pro')}</th>
            <th>{__('Type','woocommerce-stock-manager-pro')}</th>
            <th>{__('Regular price','woocommerce-stock-manager-pro')}</th>
            <th>{__('Sale price','woocommerce-stock-manager-pro')}</th>
            <th>{__('Manage stock','woocommerce-stock-manager-pro')}</th>
            <th>{__('Stock status','woocommerce-stock-manager-pro')}</th>
            <th>{__('Back orders','woocommerce-stock-manager-pro')}</th>
            <th>{__('Stock','woocommerce-stock-manager-pro')}</th>
            <th>{__('Subscriber No.','woocommerce-stock-manager-pro')}</th>
          </tr>
        </thead>
        <tbody>{renderRows()}</tbody>
      </table>
      <div className="pagination">
        {pageNumbers.map((number) => (
          <span key={number} onClick={() => paginate(number)}>
            {number}
          </span>
        ))}
      </div>
    </React.Fragment>
  );
};

export default ProductTable;
