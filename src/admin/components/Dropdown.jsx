import React, { useState } from 'react';
import axios from 'axios';
const Dropdown = ({ backorder,id,value,option1,option2,option3 }) => {
  const [orderStatus, setOrderStatus] = useState('');
  const handleDropdownChange = (e) => {
    if(backorder){
        const data ={
            id: id,
            str:true,
            name:"set_backorders",
            value: e.target.value
        }
        axios({
            method: 'post',
            url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/update`,
            data: data
        })
        setOrderStatus(e.target.value);
    }else{
      const data ={
        id: id,
        value: e.target.value,
        name:"stock_status"
      }
      axios({
          method: 'post',
          url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/update`,
          data: data
      })
      setOrderStatus(e.target.value);
    }

  };
  function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
  return (
    <div className='custom-select'>
      <select
        value={orderStatus?orderStatus:value}
        onChange={handleDropdownChange}
      >
        <option value={option1}>{capitalizeFirstLetter(option1)}</option>
        <option value={option2}>{capitalizeFirstLetter(option2)}</option>
        <option value={option3}>{capitalizeFirstLetter(option3)}</option>
      </select>
    </div>
  );
};

export default Dropdown;
