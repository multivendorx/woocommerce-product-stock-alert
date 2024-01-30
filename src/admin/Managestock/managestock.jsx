import axios from 'axios';
import React,{useEffect,useState} from 'react';
import DataTable from 'react-data-table-component';
import Checkbox from '../components/Checkbox.jsx';
import Cell from '../components/Cell.jsx';
import Dropdown from '../components/Dropdown.jsx'
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';
import ImportExport from '../ImportExport/ImportExport.jsx';

const Managestock = () => {
    const [skuFilter, setSkuFilter] = useState('');
    const [nameFilter, setNameFilter] = useState('');
    const [selectedProductType, setSelectedProductType] = useState('');
    const [selectedStockStatus, setSelectedStockStatus] = useState('');
    const [data,setData]= useState([]);
    const [model,setModel]=useState(false);
    const [inputValue, setInputValue] = useState('');
    
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
    const style={
        width: '50px',
        height: '50px',
    } 
    const handleInputChange = (e,id,str) => {
        // data.forEach((product)=>{
        //     if(product['product_id']===id){
                setData((prevData)=> {
                   return prevData.map((obj)=>{
                        if(obj.product_id===id){
                            return {...obj, [str]:e.target.value};
                        }
                        return obj;
                    })
                });
        //     }
        // })
        // setInputValue(e.target.value);
    }
    const handleImportExport = () => {
        let page=document.querySelector('.woo-subscriber-list');
        page.removeChild(page.children[0]);
        ReactDOM.render(<ImportExport data={data} />, page);        
    }
    const getFilteredData = () => {
        let modifyData = [...data];
        if(skuFilter){
            modifyData = modifyData.filter(item =>item.product_sku.toLowerCase().includes(skuFilter.toLowerCase()))
        }
        if(nameFilter){
            modifyData = modifyData.filter(item =>item.product_name.toLowerCase().includes(nameFilter.toLowerCase()))
        }
        if(selectedStockStatus){
            modifyData = modifyData.filter(item =>(item.product_stock_status === selectedStockStatus))
        }
        if(selectedProductType){
            modifyData = modifyData.filter(item =>(item.product_type === selectedProductType))
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
                <img src={row.product_image} style={style} className={"table-image"}/>
            )
        },
        {
            name: "Product Type",
            selector: (row) => row.product_type,
        },
        {
            name: "SKU",
            cell: (row) => (
                <input type="text" value={row.product_sku} name={"set_sku"} onChange={(e) => {handleInputChange(e,row.product_id,"product_sku")}} />
                // <Cell value={row.product_sku} name={"set_sku"} id={row.product_id}/>
            )
        },
        {
            name: "Regular Price",
            cell: (row) => {
                if (row.product_type=="simple") {
                    return <Cell str={false} value={row.product_regular_price} name={"set_regular_price"} id={row.product_id}/>;
                }
                
            }
        },
        {            
            name: "Sale Price",
            cell: (row) => {
                if (row.product_type=="simple") {
                    return <Cell str={false} value={row.product_sale_price} name={"set_sale_price"} id={row.product_id} />;
                }
            }
        },
        {
            name: "Weight",
            cell: (row) => (
                <Cell value={row.product_weight} name={"set_weight"} id={row.product_id} />
            )
        },
        {
            name: "Manage Stock",
            cell: (row) => (
                <Checkbox name={"set_manage_stock"} id={row.product_id} state={row.product_manage_stock?true:false} />
            )
        },
        {
            name: "Stock Status",
            cell: (row) => {
                if (row.product_manage_stock) {
                    return`${row.product_stock_status}`;
                }else {
                    return <Dropdown backorder={false} id={row.product_id} value={row.product_stock_status} option1={"instock"} option2={"onbackorder"} option3={"outofstock"} />;
                }
              },
        },
        {
            name: "Backorders",
            cell: (row) => {
                if (row.product_manage_stock) {
                  return <Dropdown backorder={true} id={row.product_id} value={row.product_backorders} option1={"no"} option2={"notify"} option3={"yes"} />;
                }else {
                  return`${row.product_backorders}`;
                }
            }
        },
        {
            name: "Stock",
            cell: (row) => {
                if (row.product_manage_stock) {
                  return <Cell str={false} value={row.product_stock_quantity} name={"set_stock_quantity"} id={row.product_id} />;
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
                    open={model}
                    onClose={() => {setModel(false)}}
                    aria-labelledby="form-dialog-title"
                >	
                    <span 
                        className="icon-cross stock-manager-popup-cross"
                        onClick={() => {setModel(false)}}
                    ></span>
                    <Popoup/>
                </Dialog>
                <img
                    src={ stockManagerAppLocalizer.manage_stock }
                    alt="subscriber-list"
                    className='subscriber-img'
                    onClick={() => {setModel(true)}}
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
                                        value={nameFilter}
                                        onChange={(e) => setNameFilter(e.target.value)}
                                    />
                                </div>
                                <div class="woo-header-search-section">
                                    <input
                                        type="text"
                                        placeholder="Search by SKU..."
                                        value={skuFilter}
                                        onChange={(e) =>setSkuFilter(e.target.value)}
                                    />
                                </div>
                                <div class="custom-select">
                                    <select
                                        value={selectedProductType?selectedProductType:""}
                                        onChange={(e) => setSelectedProductType(e.target.value)}
                                    >
                                        <option value="">Product Type</option>
                                        <option value="simple">Simple</option>
                                        <option value="variable">Variable</option>
                                    </select>
                                </div>
                                <div class="custom-select">
                                    <select
                                        value={selectedStockStatus?selectedStockStatus:""}
                                        onChange={(e) => setSelectedStockStatus(e.target.value)}
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