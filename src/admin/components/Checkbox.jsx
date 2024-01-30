import React,{useState} from 'react';
import axios from 'axios';
import Dropdown from './Dropdown';
import Cell from './Cell';

const Checkbox = ({id,name,state})=>{
    const [isChecked, setIsChecked] = useState(state);
    const handleCheckboxChange = (e) => {
        setIsChecked(e.target.checked);
        const data ={
            id: id,
            str:true,
            name: name,
            value:e.target.checked
        }
        axios({
            method: 'post',
            url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/update`,
            data: data
        })
        let row =e.target.parentElement.parentElement; // row containing all element
        let stock_status = row.children[8];
        let back_orders = row.children[9];
        let stock_quantity = row.children[10];
        if(e.target.checked){
            back_orders.innerHTML = "";
            stock_status.innerHTML = "outofstock";
            ReactDOM.render(<Cell name={"set_stock_quantity"} id={id}/>,stock_quantity)
            ReactDOM.render(<Dropdown backorder={true} id={id} value={"no"} option1={"no"} option2={"notify"} option3={"yes"} />, back_orders);
        }else{
            back_orders.innerHTML = "no";
            stock_quantity.innerHTML = "";
            ReactDOM.render(<Dropdown backorder={false} id={id} value={"outofstock"} option1={"instock"} option2={"onbackorder"} option3={"outofstock"} />, stock_status);
        }
    };
    const style={
    }
    return(
        <input
        type="checkbox"
        checked={isChecked}
        onChange={handleCheckboxChange}
      />
    )
}

export default Checkbox;