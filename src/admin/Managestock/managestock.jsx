import axios from 'axios';
import { __ } from '@wordpress/i18n';
import Dialog from "@mui/material/Dialog";
import React,{useEffect,useState} from 'react';
import Popoup from '../PopupContent/PopupContent';
import DataTable from 'react-data-table-component';
import InputElement from './InputElement';
import { BrowserRouter as Router,  Link} from 'react-router-dom';
import CustomDataTable from './CustomDatatable';

const Managestock = () => {
    const getDataUrl = `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/manage-stock`;
    const updateDataUrl = `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/update`;

    const [ filter, setFilter ] = useState({
        sku: '',
        name: '',
        productType: '',
        stockStatus: '',
    });

    const [ uploadData, setUploadData ] = useState({
        id: '',
        name: '',
        value: '',
    });

    const [ event, setEvent ] = useState();
    const [ data, setData ] = useState([]);
    const [ openDialog, setOpenDialog ] = useState(false);
    const [ inputChange, setInputChange ] = useState(false);
    
    useEffect(() => {
        if( stockManagerAppLocalizer.pro_active != 'free' ){
            axios({
                url: getDataUrl,
            }).then((response) => {
                let products = JSON.parse(response.data);
                setData(products);
            });
        }
    }, []);

    useEffect(() => {
        const submitData = async () => {
            let name = uploadData.name
            if( name !== "" || name === "set_manage_stock" || name === "set_backorders" || name === "stock_status" ){
                changeData();
            }
        };    
        submitData();
    }, [uploadData]);

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    function changeData(){
        axios({
            method: 'post', 
            url: updateDataUrl,
            headers: { 'X-WP-Nonce' : stockManagerAppLocalizer.nonce },
            data: uploadData,
        })
    }
    function dynamicWidth(value) {
        if(value.length > 11){
            return `${value.length * 14 }px`;
        }
        else if(value.length >= 5){
            return `${value.length * 9 }px`;
        }
        else if(value.length > 2){
            return `${value.length * 12}px`;
        }else{
            return `35px`;
        }
    }

    function setfilter( name, value ){
        setFilter({
            ...filter,
            [name]: value,
        });
    }

    function updateData(id,name,value){
        setUploadData({
            ['id']: id,
            ['name']: name,
            ['value']: value,
        })
    }

    function setVariationData(parent_id,variation_id,str,value){
        setData((prevData) => {
            return prevData.map((product) => {
                if (product.product_id === Number(parent_id)) {
                    return {
                        ...product,
                        variation: product.variation.map((variations) => {
                            if (variations.product_id === variation_id) {
                                return { ...variations, [str]: value };
                            }
                            return variations;
                        }),
                    };
                }
                return product;
            });
        });
    }

    const handleDocumentClick = (e) => {
        if(inputChange){
            changeData();
            setInputChange(false);
            event.classList.add('input-field-edit');
            event.setAttribute('readonly', 'readonly');
            document.removeEventListener('click', handleDocumentClick );
        }
    }

    const handleChange = (e,id,str,type) => {
        let element = e.target;
        let name = element.name;
        let Value = ( name === "set_manage_stock" ) ? element.checked : element.value;
        updateData(id,name,Value);
        if(name === "set_sale_price"){
            const regular_price = Number(element.parentElement.parentElement.parentElement.children[5].children[0].children[0].value)
            if(Value>=regular_price){
                element.parentElement.children[2].style.display = "block";
            }else{
                element.parentElement.children[2].style.display = "none";
            }
        }
        if(name === 'set_regular_price'){
            const sale_price = element.parentElement.parentElement.nextSibling.children[0].children[0].value;
            if(Number(sale_price)>Number(Value)){
                setData((prevData) => {
                    return prevData.map((obj) => {
                        if( obj.product_id === id ){
                            return {...obj, ['product_sale_price'] : "0" };
                        }
                        return obj;
                    })
                });
            }
        }
        if(name === 'set_manage_stock' && Value === true){
            if(type === 'variation'){
                setVariationData(element.id,id,"product_stock_quantity",0)
            }
            setData((prevData) => {
                return prevData.map((obj) => {
                    if( obj.product_id === id ){
                        return {...obj, ["product_stock_quantity"] : 0 };
                    }
                    return obj;
                })
            });
        }
        if( name !== "set_manage_stock" && name !== "set_backorders" && name !== "stock_status" ){
            setInputChange(true);
            setEvent(e.target)
            Value= Value.replace(/^0+/, "");
        }
        if(type === "variation"){
            setVariationData(element.id,id,str,Value)
        }else{
            setData((prevData) => {
                return prevData.map((obj) => {
                    if( obj.product_id === id ){
                        return {...obj, [str] : Value };
                    }
                    return obj;
                })
            });
        }
    }    
    
    const editButtonOnClick = (e) => {
        let element = e.currentTarget;
        element.previousSibling.focus();
        element.previousSibling.removeAttribute('readonly');
        element.previousSibling.classList.remove('input-field-edit');
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

    const getFilteredData = () => {
        let modifyData = [...data];
        if(filter.sku){
            modifyData = modifyData.filter(item => item.product_sku.toLowerCase().includes(filter.sku.toLowerCase()));
        }
        if(filter.name){
            modifyData = modifyData.filter(item => item.product_name.toLowerCase().includes(filter.name.toLowerCase()));
        }
        if(filter.stockStatus){
            modifyData = modifyData.filter(item => (item.product_stock_status === filter.stockStatus));
        }
        if(filter.productType){
            modifyData = modifyData.filter(item => (item.product_type === filter.productType));
        }
        return modifyData;
    }

    // const ExpandableRow = ({ data }) => {
    //     return (
    //         <DataTable expandableRows={true} expandableRowDisabled={ () => true } className="expanded-data-table" noHeader noTableHead columns={columns} data={data.variation}/>
    //     );
    // };
    const expandableRowsComponent = ({ row }) => {
        return(
            <CustomDataTable pagination={false} renderHeader={false} expandableRowDisabled={ () => false } expandableRowsComponent={expandableRowsComponent} data={row.variation} columns={columns} />
        );
    };

    const columns = [
        {
            name: __(<span class="dashicons img-icon dashicons-format-image"></span>,'woocommerce-stock-manager-pro'),
            cell: (row) => {
                return  <a href={row.product_link} target="_blank">
                            <img src={row.product_image} class="table-image"/>
                        </a>
            },
        },
        {
            name: __('Name','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if(row.product_type === "variation"){
                    return <input  type="text" class={`input-field input-field-edit`}  value={row.product_name} readOnly/>;
                }else{
                    return  <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input id={( row.product_type === "variation" ) ? row.parent_product_id : null} type="text" class="input-field input-field-edit"  style={{ width: dynamicWidth(row.product_name) }} value={row.product_name} name={"set_name"} onChange={(e) => {handleChange(e, row.product_id, "product_name", row.product_type)}} readOnly />
                                <span onClick={editButtonOnClick} class="edit-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                                </span>
                            </div> 
                }
            },
        },
        {
            name: __('SKU','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if(row.product_type === "variation"){
                    return <InputElement setVariationData={setVariationData} editButtonOnClick={editButtonOnClick} dynamicWidth={dynamicWidth} row={row} value={row.product_sku} id={row.product_id} get_name={"product_sku"} set_name={"set_sku"}/>
                }else{
                    return  <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input id={(row.product_type === "variation") ? row.parent_product_id : null } type="text" class="input-field input-field-edit" style={{ width: dynamicWidth(row.product_sku) }}  value={row.product_sku} name={"set_sku"} onChange={(e) => {handleChange(e, row.product_id, "product_sku", row.product_type)}} readOnly />
                                <span onClick={editButtonOnClick} class="edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                                </span>
                            </div>
                }
            },
        },
        {
            name: __('Type','woocommerce-stock-manager-pro'),
            cell: (row) => {
                return capitalizeFirstLetter(row.product_type);
            }
        },
        {
            name: __('Regular price','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_type === "simple") {
                    return  <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input id={(row.product_type === "variation") ? row.parent_product_id : null } style={{ width: dynamicWidth(row.product_regular_price) }} type="number" min={0} class="input-field input-field-edit"  value={(row.product_regular_price === "" || row.product_regular_price === null) ? 0 : row.product_regular_price} name={"set_regular_price"} onChange={(e) => {handleChange(e, row.product_id, "product_regular_price",row.product_type)}} readOnly />                        
                                <span onClick={editButtonOnClick} className='edit-btn'>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                                </span>
                            </div>
                }else if(row.product_type === "variation"){
                    return <InputElement setVariationData={setVariationData} editButtonOnClick={editButtonOnClick} dynamicWidth={dynamicWidth} row={row} value={row.product_regular_price} id={row.product_id} get_name={"product_regular_price"} set_name={"set_regular_price"}/>
                }else if( row.product_type === "variable" ){
                    return  <div className="cell">
                                <input  type="number" class={`input-field input-field-edit`} style={{ width: dynamicWidth(row.variation_regular_price_min) }}  value={row.variation_regular_price_min} readOnly/>-
                                <input  type="number" class={`input-field input-field-edit`} style={{ width: dynamicWidth(row.variation_regular_price_max) }}  value={row.variation_regular_price_max} readOnly/>
                            </div>
                }
            },
        },
        {
            name: __('Sale price','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_type === "simple") {
                    return <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input id={(row.product_type === "variation") ? row.parent_product_id : null} style={{ width: dynamicWidth(row.product_sale_price) }} type="number" min={0} class="input-field input-field-edit"  value={( row.product_sale_price === "" ) ? 0 : row.product_sale_price} name={"set_sale_price"} onChange={(e) => { handleChange( e, row.product_id, "product_sale_price", row.product_type ) }} readOnly />                        
                                <span onClick={editButtonOnClick} class="edit-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                                </span>
                                <div className='sale-price-error-message'>
                                    Please enter in a value less than the regular price
                                </div>
                            </div>
                }else if(row.product_type === "variation"){
                    return <InputElement setVariationData={setVariationData} editButtonOnClick={editButtonOnClick} dynamicWidth={dynamicWidth} row={row} value={row.product_sale_price} id={row.product_id} get_name={"product_sale_price"} set_name={"set_sale_price"}/>
                }else if(row.product_type === "variable"){
                    return  <div className="cell">
                                <input  type="number" class={`input-field input-field-edit`} style={{ width: dynamicWidth(row.variation_sale_price_min) }}  value={row.variation_sale_price_min} readOnly/>-
                                <input  type="number" class={`input-field input-field-edit`} style={{ width: dynamicWidth(row.variation_sale_price_max) }}  value={row.variation_sale_price_max} readOnly/>
                            </div>
                }          
            },
        },
        {
            name: __('Manage stock','woocommerce-stock-manager-pro'),
            cell: (row) => (
                <div className='custome-toggle-default'>
                    <input id={( row.product_type === "variation" ) ? row.parent_product_id : row.product_id }  type="checkbox" name={"set_manage_stock"} checked={row.product_manage_stock} onChange={(e) => {handleChange( e, row.product_id, "product_manage_stock", row.product_type )}} />
                    <label htmlFor={row.product_id}></label>
                </div>
            ),
        },
        {
            name: __('Stock status','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_manage_stock) {
                    if( row.product_stock_quantity > 0 || row.variation_stock_quantity > 0 ){
                        return <p class="value-sucess">{__("In stock","woocommerce-stock-manager-pro")}</p>;
                    }
                    return <p class="value-danger">{__("Out of stock","woocommerce-stock-manager-pro")}</p>;
                }else {
                    return  <div className='custom-select'>
                                <select id={(row.product_type === "variation") ? row.parent_product_id : null }   name='stock_status' value={row.product_stock_status} onChange={(e) => {handleChange( e, row.product_id, "product_stock_status", row.product_type )}} >
                                    <option value={"instock"}>{__("In stock","woocommerce-stock-manager-pro")}</option>
                                    <option value={"onbackorder"}>{__("On backorder","woocommerce-stock-manager-pro")}</option>
                                    <option value={"outofstock"}>{__("Out of stock","woocommerce-stock-manager-pro")}</option>
                                </select>
                            </div>
                }
            },
        },
        {
            name: __('Back orders','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_manage_stock) {
                    return  <div className='custom-select'>
                                <select id={(row.product_type === "variation") ? row.parent_product_id : null }  name='set_backorders' value={row.product_backorders} onChange={(e) => {handleChange(e, row.product_id, "product_backorders", row.product_type )}}>
                                    <option value={"no"}>{__("No","woocommerce-stock-manager-pro")}</option>
                                    <option value={"notify"}>{__("Notify","woocommerce-stock-manager-pro")}</option>
                                    <option value={"yes"}>{__("Yes","woocommerce-stock-manager-pro")}</option>
                                </select>
                            </div>
                }else{
                  return <p>{__("No","woocommerce-stock-manager-pro")}</p>;
                }
            },
        },
        {
            name: "Stock",
            cell: (row) => {
                if ( row.product_type === "simple" && row.product_manage_stock ){
                    return <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input id={( row.product_type === "variation" ) ? row.parent_product_id : null } style={{ width: row.product_stock_quantity !== null ? dynamicWidth(row.product_stock_quantity) : '35px' }} type="number" min={0} class={`input-field input-field-edit ${( row.product_stock_quantity > 0 ) ? "value-sucess" : "value-danger"}`}  value={( row.product_stock_quantity === null ) ? '0' : row.product_stock_quantity} name={"set_stock_quantity"} onChange={(e) => {handleChange(e, row.product_id, "product_stock_quantity", row.product_type)}} readOnly/>                        
                                <span onClick={editButtonOnClick} class="edit-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" id="edit"><path fill="#212121" d="M12.2417871,6.58543288 L6.27024769,12.5583865 C5.94985063,12.8787836 5.54840094,13.1060806 5.1088198,13.2159758 L2.81782051,13.7887257 C2.45163027,13.8802732 2.11993389,13.5485768 2.21148144,13.1823866 L2.78423127,10.8913873 C2.89412655,10.4518062 3.12142351,10.0503565 3.44182056,9.72995942 L9.41336001,3.75700576 L12.2417871,6.58543288 Z M13.6567078,2.3434993 C14.4377564,3.12454789 14.4377564,4.39087785 13.6567078,5.17192643 L12.9488939,5.8783261 L10.1204668,3.04989898 L10.8282807,2.3434993 C11.6093293,1.56245072 12.8756592,1.56245072 13.6567078,2.3434993 Z"></path></svg>
                                </span>
                            </div>
                }else if( row.product_type === "variation" && row.product_manage_stock ){
                    return <InputElement setVariationData={setVariationData} editButtonOnClick={editButtonOnClick} dynamicWidth={dynamicWidth} row={row} value={row.product_stock_quantity} id={row.product_id} get_name={"product_stock_quantity"} set_name={"set_stock_quantity"}/>
                }else if( row.product_type === "variable" ){
                    return <input  type="number" min={0} class={`input-field input-field-edit ${ (row.variation_stock_quantity>0) ? "value-sucess" : "value-danger"}`}  value={ (row.variation_stock_quantity === null) ? '0' : row.variation_stock_quantity } readOnly/> 
                }
            },
        },
        {
            name: __('Subscriber No.','woocommerce-stock-manager-pro'),
            cell: (row) => {
                return  <div class="cell">
                            {row.product_interested_person}
                        </div> 
            },
        }
    ];
    return( 
    <>
        { stockManagerAppLocalizer.pro_active === 'free'  ?
            <div>
                <Dialog
                    className="woo-module-popup"
                    open={openDialog}
                    onClose={() => {setOpenDialog(false)}}
                    aria-labelledby="form-dialog-title"
                >
                    <span 
                        className="icon-cross stock-manager-popup-cross"
                        onClick={() => {setOpenDialog(false)}}
                    ></span>
                    <Popoup/>
                </Dialog>
                <img
                    src={ stockManagerAppLocalizer.manage_stock }
                    alt="subscriber-list"
                    className='subscriber-img'
                    onClick={() => {setOpenDialog(true)}}
                />
            </div>
        :
            <div className="woo-subscriber-list">
                <div className="woo-container">
                    <div className="woo-middle-container-wrapper">
                        <div className="woo-search-and-multistatus-wrap">
                            <div className="woo-page-title">
                                <p>{__('Inventory Manager','woocommerce-stock-manager-pro')}</p>
                            </div>
                        </div>
                        <div className="woo-search-and-multistatus-wrap">
                            <div class="woo-wrap-bulk-all-date">
                                <div class="woo-header-search-section">
                                    <input
                                        type="text"
                                        placeholder="Search by Name..."
                                        value={filter.name}
                                        onChange={(e) => setfilter('name',e.target.value)}
                                    />
                                </div>
                                <div class="woo-header-search-section">
                                    <input
                                        type="text"
                                        placeholder="Search by SKU..."
                                        value={filter.sku}
                                        onChange={(e) =>setfilter('sku',e.target.value)}
                                    />
                                </div>
                                <div class="custom-select">
                                    <select
                                        value={filter.productType}
                                        onChange={(e) => setfilter('productType',e.target.value)}
                                    >
                                        <option value="">{__("Product Type","woocommerce-stock-manager-pro")}</option>
                                        <option value="simple">{__("Simple","woocommerce-stock-manager-pro")}</option>
                                        <option value="variable">{__("Variable","woocommerce-stock-manager-pro")}</option>
                                    </select>
                                </div>
                                <div class="custom-select">
                                    <select
                                        value={filter.stockStatus}
                                        onChange={(e) => setfilter('stockStatus',e.target.value)}
                                    >
                                        <option value="" >{__("Stock Status","woocommerce-stock-manager-pro")}</option>
                                        <option value="instock">{__("In stock","woocommerce-stock-manager-pro")}</option>
                                        <option value="onbackorder">{__("On backorder","woocommerce-stock-manager-pro")}</option>
                                        <option value="outofstock">{__("Out of stock","woocommerce-stock-manager-pro")}</option>
                                    </select>
                                </div>
                            </div>
                            <div className='stock-reports-download'>
                                <div className="pull-right import">                                    
                                    <button class="import-export-btn" >
                                        <Link to={'?page=woo-stock-manager-setting#&tab=import'}>
                                            <div className='wp-menu-image dashicons-before dashicons-download'></div>
                                            {__("Import","woocommerce-stock-manager-pro")}</Link>
                                    </button>
                                </div>
                                <div className="pull-right export">
                                    <button class="import-export-btn" >
                                        <Link to={'?page=woo-stock-manager-setting#&tab=export'}>
                                            <div className='wp-menu-image dashicons-before dashicons-upload'></div>
                                            {__("Export","woocommerce-stock-manager-pro")}
                                        </Link>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div className="woo-backend-datatable-wrapper">
                            {/* <DataTable
                            columns={columns}
                            expandableRows
                            expandableRowsComponent={ExpandableRow}
                            expandableRowDisabled={(row) => !(row.variation.length>0)}
                            data={getFilteredData()}
                            /> */}
                            <CustomDataTable pagination={true} renderHeader={true} expandableRowDisabled={(row) => (row.variation.length>0)} expandableRowsComponent={expandableRowsComponent} data={getFilteredData()} columns={columns} />
                        </div>
                    </div>
                </div>
            </div>
        }
    </>
    );
};
export default Managestock;