import React, { useState } from 'react';

const CustomDataTable = ({ columns, data, expandableRowsComponent, pagination, renderHeader,expandableRowDisabled, itemsPerPageOptions = [10, 20, 30], defaultItemsPerPage = 5 }) => {
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(defaultItemsPerPage);
  const [expandedRows, setExpandedRows] = useState([]);

  const totalItems = data.length;
  const totalPages = Math.ceil(totalItems / itemsPerPage);

  const startIndex = (currentPage - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  const currentData = data.slice(startIndex, endIndex);
  
  const renderHeaderCells = () => {
    const dropDownHeader = <th>ab</th>
    const headers = columns.map((column) => (
      <th key={column.name}>
        {column.name}
      </th>
    ));
    return [dropDownHeader,...headers]
  };

  const handleExpandToggle = (index) => {
    setExpandedRows((prevExpandedRows) => {
      const isExpanded = prevExpandedRows.includes(index);
      return isExpanded
        ? prevExpandedRows.filter((rowIndex) => rowIndex !== index)
        : [...prevExpandedRows, index];
    });
  };

  const isExpandableRowDisabled = (row) => {
    return expandableRowDisabled && expandableRowDisabled(row);
  };

  const renderRows = () => {
    return currentData.map((row, index) => (
      <React.Fragment key={startIndex + index}>
        <tr className='table table-row'>
          <td>
            {(isExpandableRowDisabled(row))?
            <button onClick={() => handleExpandToggle(startIndex + index)}>
              {expandedRows.includes(startIndex + index) ? 'Collapse' : 'Expand'}
            </button>:""}
          </td>
          {columns.map((column) => (
            <td key={column.name}>
              {column.cell ? column.cell(row) : row[column.key]}
            </td>
          ))}
        </tr>
        {expandedRows.includes(startIndex + index) && (
          <tr>
            <td colSpan={columns.length + 1}>
              {expandableRowsComponent({ row })}
            </td>
          </tr>
        )}
      </React.Fragment>
    ));
  };

  const handlePageChange = (pageNumber) => {
    setCurrentPage(pageNumber);
  };

  const handleItemsPerPageChange = (value) => {
    setItemsPerPage(value);
    setCurrentPage(1);
  };

  const renderPagination = () => {
    return (
      <div>
        <span>{`Page ${currentPage} of ${totalPages}`}</span>
        <button onClick={() => handlePageChange(currentPage - 1)} disabled={currentPage === 1}>
          Previous
        </button>
        <button onClick={() => handlePageChange(currentPage + 1)} disabled={currentPage === totalPages}>
          Next
        </button>
        <span>Show
          <select value={itemsPerPage} onChange={(e) => handleItemsPerPageChange(Number(e.target.value))}>
            {itemsPerPageOptions.map((option) => (
              <option key={option} value={option}>
                {option}
              </option>
            ))}
          </select>
          rows per page
        </span>
      </div>
    );
  };

  return (
    <div>
      {renderHeader && <thead><tr>{renderHeaderCells()}</tr></thead>}
      <tbody>{renderRows()}</tbody>
      {pagination && <div>{renderPagination()}</div>}
    </div>
  );
};

export default CustomDataTable;
