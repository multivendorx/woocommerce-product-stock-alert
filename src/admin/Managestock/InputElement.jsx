import React,{useState} from 'react'
import axios from 'axios';

const InputElement = ({row, id, value, get_name, dynamicWidth, set_name, setVariationData, editButtonOnClick }) => {
  
  const updateDataUrl = `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/update`;
  const [inputChange,setInputChange] = useState(false);
  const [inputValue, setInputValue] = useState(value);
  const [uploadData,setUploadData] = useState({
    id: '',
    parent_id: '',
    name: '',
    value: '',
  });
  
  function updateData(id,parent_id,name,value){
    setUploadData({
      ['id']: id,
      ['parent_id']:parent_id,
      ['name']: name,
      ['value']: value,
    })
  }

  const handleInputChange = (e) => {
    let element = e.target;
    let Value = e.target.value;
    if(set_name !== "set_sku"){
      Value= Value.replace(/^0+/, "");
    }
    updateData(id,element.id,set_name,Value);
    setInputChange(true);
    setInputValue(Value);
    if(element.name === "set_sale_price"){
      const regular_price = Number(element.parentElement.parentElement.parentElement.children[5].children[0].children[0].value)
      if(Value>=regular_price){
          element.parentElement.children[2].style.display = "block";
      }else{
          element.parentElement.children[2].style.display = "none";
      }
    }
  };

  const handleDocumentClick = (e) => {
    if(inputChange){
      axios({
        method: 'post', 
        url: updateDataUrl,
        headers: { 'X-WP-Nonce' : stockManagerAppLocalizer.nonce },
        data: uploadData,
      })
      setVariationData(uploadData.parent_id,id,get_name,uploadData.value)
      setInputChange(false);
      document.removeEventListener('click', handleDocumentClick );
    }
  }
  const handleInputMouseOut = (e) => {
    e.currentTarget.children[1].style.display = 'none';
    if(inputChange){
        document.addEventListener( 'click', handleDocumentClick );
    }
  }
  const handleInputMouseOver = (e) => {
    e.currentTarget.children[1].style.display = 'flex';
  }
  return (
    <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
        <input id={row.parent_product_id} style={{ width: value !== null ? dynamicWidth(inputValue) : '30px' }} type={ (set_name ==="set_sku") ? "text" : "number" } min={0} class={`input-field input-field-edit ${ (set_name ==="set_stock_quantity" && inputValue > 0 ) ? "value-sucess" : "" } ${(set_name ==="set_stock_quantity" && 0 >= inputValue) ? "value-danger" : ""}`}  value={(inputValue === "" || inputValue === null) ? 0 : inputValue} name={set_name} onChange={handleInputChange} readOnly />
        <span onClick={editButtonOnClick} className='edit-btn'>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
        </span>
        {set_name === "set_sale_price"?
          <div className='sale-price-error-message'>
          Please enter in a value less than the regular price
          </div>:""
        }
    </div>
  )
}
export default InputElement;