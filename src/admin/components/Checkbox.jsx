import React,{useState} from 'react';
import axios from 'axios';
import Dropdown from './Dropdown';
import Cell from './Cell';

const Checkbox = ({changeData,id,name,state})=>{
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
            url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager/v1/update`,
            data: data
        })
        let row =e.target.parentElement.parentElement; // row containing all element
        if(e.target.checked){
            let stock_div = row.children[8].children[0]; //div containing select
            let stock_quantity = row.children[10]; 
            let stock_select_element = stock_div.children[0]; //select element
            let backorder_div =row.children[9]
            backorder_div.innerText="";
            let stock_select_element_text=stock_select_element.value;
            stock_div.innerHTML=stock_select_element.value;
            ReactDOM.render(<Cell str={false} name={"set_stock_quantity"} id={id}/>,stock_quantity)
            ReactDOM.render(<Dropdown backorder={true} id={id} value={stock_select_element_text} option1={"no"} option2={"notify"} option3={"yes"} />, backorder_div);
        }else{
            let backorder_div =row.children[9].children[0];
            let backorder_select_element = backorder_div.children[0];
            let stock_quantity = row.children[10];
            let stock_div =row.children[8]
            stock_quantity.innerHTML=" ";
            backorder_div.innerHTML="no";
            ReactDOM.render(<Dropdown backorder={false} id={id} value={"outofstock"} option1={"instock"} option2={"onbackorder"} option3={"outofstock"} />, stock_div);
        }
    };
    // const updateData = (e) =>{
    //     axios({
    //         url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager/v1/manage-stock`,
    //     }).then((response) => {
    //         let products =JSON.parse(response.data);
    //         changeData(products);
    //     });
    // }
    const style={
    }
    return(
        <input
        type="checkbox"
        // onMouseUp={updateData}
        checked={isChecked}
        onChange={handleCheckboxChange}
      />
    )
}

export default Checkbox;