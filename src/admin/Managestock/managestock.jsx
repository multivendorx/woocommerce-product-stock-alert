import axios from 'axios';
import Dialog from "@mui/material/Dialog";
import React,{useEffect,useState} from 'react';
import Popoup from '../PopupContent/PopupContent';
import DataTable from 'react-data-table-component';
import ImportExport from '../ImportExport/ImportExport.jsx';

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
                url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/manage-stock`,
            }).then((response) => {
                let products =JSON.parse(response.data);
                setData(products);
            });
        }
    }, []);
    useEffect(() => {
        const updateData = async () => {
        let name = uploadData.name
        if( name !== "" || name === "set_manage_stock" || name === "set_backorders" || name === "stock_status"){
            changeData()
        }
        };    
        updateData();
    }, [uploadData]);
    function changeData(){
        axios({
            method: 'post',
            url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/update`,
            data: uploadData,
        })
    }
    function setfilter(name,value){
        setFilter({
            ...filter,
            [name]: value,
        });
    }
    function updateData(id,name,value){
        setUploadData({
            ['id']:id,
            ['name']:name,
            ['value']:value,
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
        let element =e.target;
        let name = element.name;
        let Value = ( name === "set_manage_stock" )? element.checked:element.value;
        updateData(element.id,name,Value);
        if(name !== "set_manage_stock" || name !== "set_backorders" || name !== "stock_status"){
            setInputChange(true);
        }
        setData((prevData) => {
            return prevData.map((obj) => {
                if(obj.product_id === id){
                    return {...obj, [str] : Value};
                }
                return obj;
            })
        });
    }
    const handleInputMouseOut = (e) => {
        e.currentTarget.children[1].style.display = 'none';
        if(inputChange){
            document.addEventListener('click', handleDocumentClick);
        }
    }
    const editButtonOnClick = (e) =>{
        if(event){
            event.classList.add('input-field-edit');
            event.setAttribute('readonly','readonly');
        }
        setEvent(e.currentTarget.previousSibling);
        e.currentTarget.previousSibling.removeAttribute('readonly');
        e.currentTarget.previousSibling.classList.remove('input-field-edit');
    }
    const handleInputMouseOver = (e) => {
        e.currentTarget.children[1].style.display = 'block';
    }
    const handleImportExport = () => {
        let page=document.querySelector('.woo-subscriber-list');
        page.removeChild(page.children[0]);
        ReactDOM.render(<ImportExport data={data} />, page);        
    }
    const getFilteredData = () => {
        let modifyData = [...data];
        if(filter.sku){
            modifyData = modifyData.filter(item =>item.product_sku.toLowerCase().includes(filter.sku.toLowerCase()));
        }
        if(filter.name){
            modifyData = modifyData.filter(item =>item.product_name.toLowerCase().includes(filter.name.toLowerCase()));
        }
        if(filter.stockStatus){
            modifyData = modifyData.filter(item =>(item.product_stock_status === filter.stockStatus));
        }
        if(filter.productType){
            modifyData = modifyData.filter(item =>(item.product_type === filter.productType));
        }
        return modifyData;
    }
    const columns = [
        {
            name: "Product Name",
            selector: (row) => row.product_name,
        },
        {
            name:"Product Photo",
            cell: (row) => (
                <img src={row.product_image} class="table-image"/>
            )
        },
        {
            name: "Product Type",
            selector: (row) => row.product_type,
        },
        {
            name: "SKU",
            width: '130px',
            cell: (row) => (
                <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                    <input  type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_sku} name={"set_sku"} onChange={(e) => {handleChange(e,row.product_id,"product_sku")}} readOnly />
                    <span onClick={editButtonOnClick} class="dashicons dashicons-edit edit"></span>
                </div>
            )
        },
        {
            name: "Regular Price",
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
            name: "Sale Price",
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
            name: "Weight",
            cell: (row) => (
                <div class="cell" onMouseOver={handleInputMouseOver} onMouseOut={handleInputMouseOut}>
                    <input type="text" class="input-field input-field-edit" id={row.product_id} value={row.product_weight} name={"set_weight"} onChange={(e) => {handleChange(e,row.product_id,"product_weight")}} readOnly/>                        
                    <span onClick={editButtonOnClick} class="dashicons dashicons-edit"></span>
                </div>
            )
        },
        {
            name: "Manage Stock",
            cell: (row) => (
                <input id={row.product_id} type="checkbox" name={"set_manage_stock"} checked={row.product_manage_stock} onChange={(e) => {handleChange(e,row.product_id,"product_manage_stock")}} />
            )
        },
        {
            name: "Stock Status",
            width: '150px',
            cell: (row) => {
                if (row.product_manage_stock) {
                    return`${row.product_stock_status}`;
                }else {
                    return  <div className='custom-select'>
                                <select id={row.product_id} name='stock_status' value={row.product_stock_status} onChange={(e) => {handleChange(e,row.product_id,"product_stock_status")}} >
                                    <option value={"instock"}>Instock</option>
                                    <option value={"onbackorder"}>Onbackorder</option>
                                    <option value={"outofstock"}>Outofstock</option>
                                </select>
                            </div>
                }
            },
        },
        {
            name: "Backorders",
            cell: (row) => {
                if (row.product_manage_stock) {
                    return  <div className='custom-select'>
                                <select id={row.product_id} name='set_backorders' value={row.product_backorders} onChange={(e) => {handleChange(e,row.product_id,"product_backorders")}}>
                                    <option value={"no"}>No</option>
                                    <option value={"notify"}>Notify</option>
                                    <option value={"yes"}>Yes</option>
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
        {stockManagerAppLocalizer.pro_active === 'free'  ?
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
                                <p>Inventory Manager</p>
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
                                        <option value="">Product Type</option>
                                        <option value="simple">Simple</option>
                                        <option value="variable">Variable</option>
                                    </select>
                                </div>
                                <div class="custom-select">
                                    <select
                                        value={filter.stockStatus}
                                        onChange={(e) => setfilter('stockStatus',e.target.value)}
                                    >
                                        <option value="" >Stock Status</option>
                                        <option value="instock">Instock</option>
                                        <option value="onbackorder">Onbackorder</option>
                                        <option value="outofstock">Outofstock</option>
                                    </select>
                                </div>
                            </div>
                            <div className="pull-right export">
                                <button class="import-export-btn" onClick={handleImportExport}>Import/Export</button>
                            </div>
                        </div>
                        <div className="woo-backend-datatable-wrapper">
                            <DataTable
                            columns={columns}
                            data={getFilteredData()}
                            // selectableRows
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