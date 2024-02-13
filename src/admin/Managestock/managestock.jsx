import axios from 'axios';
import { __ } from '@wordpress/i18n';
import Dialog from "@mui/material/Dialog";
import React,{useEffect,useState} from 'react';
import Export from './Export';
import Import from './Import';
import Popoup from '../PopupContent/PopupContent';
import DataTable from 'react-data-table-component';

const Managestock = () => {
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

    const [ event,setEvent ] = useState();
    const [ data, setData ] = useState([]);
    const [ openDialog, setOpenDialog ] = useState(false);
    const [ inputChange,setInputChange ] = useState(false);
    
    useEffect(() => {
        if(stockManagerAppLocalizer.pro_active != 'free'){
            axios({
                url: `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/manage-stock`,
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

    function changeData(){
        axios({
            method: 'post',
            url: `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/update`,
            data: uploadData,
        })
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

    const handleDocumentClick = () => {
        changeData();
        setInputChange(false);
        event.classList.add('input-field-edit');
        event.setAttribute('readonly', 'readonly');
        document.removeEventListener('click', handleDocumentClick);
    }

    const handleChange = (e,id,str) => {
        let element = e.target;
        let name = element.name;
        let Value = ( name === "set_manage_stock" ) ? element.checked : element.value;
        updateData(element.id,name,Value);
        if( name !== "set_manage_stock" || name !== "set_backorders" || name !== "stock_status" ){
            setInputChange(true);
        }
        setData((prevData) => {
            return prevData.map((obj) => {
                if( obj.product_id === id ){
                    return {...obj, [str] : Value };
                }
                return obj;
            })
        });
    }

    const handleInputMouseOut = (e) => {
        e.currentTarget.children[1].style.display = 'none';
        if(inputChange){
            document.addEventListener( 'click', handleDocumentClick );
        }
    }

    const editButtonOnClick = (e) =>{
        if(event){
            event.classList.add('input-field-edit');
            event.setAttribute('readonly', 'readonly');
        }
        setEvent(e.currentTarget.previousSibling);
        e.currentTarget.previousSibling.removeAttribute('readonly');
        e.currentTarget.previousSibling.classList.remove('input-field-edit');
    }

    const handleInputMouseOver = (e) => {
        e.currentTarget.children[1].style.display = 'block';
    }

    const handleExport = () => {
        let page=document.querySelector('.woo-subscriber-list');
        page.removeChild(page.children[0]);
        ReactDOM.render(<Export data={data} />, page);        
    }
    const handleImport = () => {
        let page=document.querySelector('.woo-subscriber-list');
        page.removeChild(page.children[0]);
        ReactDOM.render(<Import/>, page);        
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

    const columns = [
        {
            name: __('Product Name','woocommerce-stock-manager-pro'),
            selector: (row) => row.product_name,
        },
        {
            name: __('Product Photo','woocommerce-stock-manager-pro'),
            cell: (row) => (
                <img src={row.product_image} class="table-image"/>
            )
        },
        {
            name: __('Product Type','woocommerce-stock-manager-pro'),
            selector: (row) => row.product_type,
        },
        {
            name: __('SKU','woocommerce-stock-manager-pro'),
            width: '130px',
            cell: (row) => (
                <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                    <input  type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_sku} name={"set_sku"} onChange={(e) => {handleChange(e,row.product_id,"product_sku")}} readOnly />
                    <span onClick={editButtonOnClick} class="dashicons dashicons-edit edit"></span>
                </div>
            )
        },
        {
            name: __('Regular Price','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_type=="simple") {
                    return  <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_regular_price} name={"set_regular_price"} onChange={(e) => {handleChange(e,row.product_id,"product_regular_price")}} readOnly />                        
                                <span onClick={editButtonOnClick} class="dashicons dashicons-edit edit"></span>
                            </div>
                }                
            }
        },
        {            
            name: __('Sale Price','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_type=="simple") {
                    return <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_sale_price} name={"set_sale_price"} onChange={(e) => {handleChange(e,row.product_id,"product_sale_price")}} readOnly />                        
                                <span onClick={editButtonOnClick} class="dashicons dashicons-edit edit"></span>
                            </div>
                }                
            }
        },
        {
            name: __('Weight','woocommerce-stock-manager-pro'),
            cell: (row) => (
                <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                    <input type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_weight} name={"set_weight"} onChange={(e) => {handleChange(e,row.product_id,"product_weight")}} readOnly/>                        
                    <span onClick={editButtonOnClick} class="dashicons dashicons-edit"></span>
                </div>
            )
        },
        {
            name: __('Manage Stock','woocommerce-stock-manager-pro'),
            cell: (row) => (
                <input id={row.product_id} type="checkbox" name={"set_manage_stock"} checked={row.product_manage_stock} onChange={(e) => {handleChange(e,row.product_id,"product_manage_stock")}} />
            )
        },
        {
            name: __('Stock Status','woocommerce-stock-manager-pro'),
            width: '150px',
            cell: (row) => {
                if (row.product_manage_stock) {
                    return`${row.product_stock_status}`;
                }else {
                    return  <div className='custom-select'>
                                <select id={row.product_id} name='stock_status' value={row.product_stock_status} onChange={(e) => {handleChange(e,row.product_id,"product_stock_status")}} >
                                    <option value={"instock"}>{__("Instock","woocommerce-stock-manager-pro")}</option>
                                    <option value={"onbackorder"}>{__("Onbackorder","woocommerce-stock-manager-pro")}</option>
                                    <option value={"outofstock"}>{__("Outofstock","woocommerce-stock-manager-pro")}</option>
                                </select>
                            </div>
                }
            },
        },
        {
            name: __('Backorders','woocommerce-stock-manager-pro'),
            cell: (row) => {
                if (row.product_manage_stock) {
                    return  <div className='custom-select'>
                                <select id={row.product_id} name='set_backorders' value={row.product_backorders} onChange={(e) => {handleChange(e,row.product_id,"product_backorders")}}>
                                    <option value={"no"}>{__("No","woocommerce-stock-manager-pro")}</option>
                                    <option value={"notify"}>{__("Notify","woocommerce-stock-manager-pro")}</option>
                                    <option value={"yes"}>{__("Yes","woocommerce-stock-manager-pro")}</option>
                                </select>
                            </div>
                }else {
                  return`${row.product_backorders}`;
                }
            }
        },
        {
            name: "Stock",
            cell: (row) => {
                if (row.product_manage_stock) {
                    return <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                                <input type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_stock_quantity} name={"set_stock_quantity"} onChange={(e) => {handleChange(e,row.product_id,"product_stock_quantity")}} />                        
                                <span onClick={editButtonOnClick} class="dashicons dashicons-edit edit"></span>
                            </div>
                }
            }
        },        
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
                                        <option value="instock">{__("Instock","woocommerce-stock-manager-pro")}</option>
                                        <option value="onbackorder">{__("Onbackorder","woocommerce-stock-manager-pro")}</option>
                                        <option value="outofstock">{__("Outofstock","woocommerce-stock-manager-pro")}</option>
                                    </select>
                                </div>
                            </div>
                            <div className="pull-right import">
                                <button class="import-export-btn" onClick={handleImport}>{__("Import","woocommerce-stock-manager-pro")}</button>
                            </div>
                            <div className="pull-right export">
                                <button class="import-export-btn" onClick={handleExport}>{__("Export","woocommerce-stock-manager-pro")}</button>
                            </div>
                        </div>
                        <div className="woo-backend-datatable-wrapper">
                            <DataTable
                            columns={columns}
                            data={getFilteredData()}
                            pagination
                            />
                        </div>
                    </div>
                </div>
            </div>
        }
    </>
    );
};
export default Managestock;