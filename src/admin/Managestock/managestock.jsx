import axios from 'axios';
import React,{useEffect,useState} from 'react';
import DataTable from 'react-data-table-component';
import Checkbox from '../components/Checkbox.jsx';
import Cell from '../components/Cell.jsx';
import Image from '../components/Image.jsx';
import Dropdown from '../components/Dropdown.jsx'
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';

const Managestock = () => {
    const [skuFilter, setSkuFilter] = useState('');
    const [nameFilter, setNameFilter] = useState('');
    const [selectedProductType, setSelectedProductType] = useState('');
    const [selectedStockStatus, setSelectedStockStatus] = useState('');
    const [data,setData]= useState([]);
    const [model,setModel]=useState(false);
    // this.state = {
    //     open_model: false,
    // }
    const handleClose = ()=>{
        setModel(false);
		// this.setState({
		// 	open_model: false,
		// });
	}

	const handleCloseDialog = ()=>{
        setModel(false);
		// this.setState({
		// 	open_model: false,
		// });
	}

	const CheckProActive = () => {
		if (stockManagerAppLocalizer.pro_active == 'free' ) {
            setModel(true);
			// this.setState({
			// 	open_model: true,
			// });
		}
	}
    useEffect(() => {
        if(stockManagerAppLocalizer.pro_active != 'free'){
            axios({
                url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/manage-stock`,
            }).then((response) => {
                let products =JSON.parse(response.data);
                setData(products);
            });
        }
        // console.log("loaded");
    }, []);
    const getFilteredData= ()=>{
        var modifyData = [...data];
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
        return modifyData
    }
    const columns= [
        {
            name:"Product Name",
            selector:(row)=>row.product_name,
        },
        {
            name:"Product Photo",
            cell: (row) =>(
                <Image src={row.product_image}></Image>
            )
        },
        {
            name: "Product Type",
            selector:(row)=>row.product_type,
        },
        {
            name:"SKU",
            selector:(row)=>row.product_sku,
            cell: (row) => (
                <Cell str={true} value={row.product_sku} name={"set_sku"} id={row.product_id}/>
            )
        },
        {
            name:"Regular Price",
            cell: (row) => {
                if (row.product_type=="simple") {
                    return <Cell str={false} value={row.product_regular_price} name={"set_regular_price"} id={row.product_id}/>;
                }
                
            }
        },
        {            
            name:"Sale Price",
            cell: (row) => {
                if (row.product_type=="simple") {
                    return <Cell str={false} value={row.product_sale_price} name={"set_sale_price"} id={row.product_id} />;
                }
            }
        },
        {
            name:"Weight",
            cell: (row) => (
                <Cell str={false} value={row.product_weight} name={"set_weight"} id={row.product_id} />
            )
        },
        {
            name:"Manage Stock",
            cell: (row) => (
                <Checkbox changeData={getFilteredData} name={"set_manage_stock"} id={row.product_id} state={row.product_manage_stock?true:false} />
            )
        },
        {
            name:"Stock Status",
            cell: (row) => {
                if (row.product_manage_stock) {
                    return`${row.product_stock_status}`;
                }else {
                    return <Dropdown backorder={false} id={row.product_id} value={row.product_stock_status} option1={"instock"} option2={"onbackorder"} option3={"outofstock"} />;
                }
              },
        },
        {
            name:"Backorders",
            cell: (row) => {
                if (row.product_manage_stock) {
                  return <Dropdown backorder={true} id={row.product_id} value={row.product_backorders} option1={"no"} option2={"notify"} option3={"yes"} />;
                }else {
                  return`${row.product_backorders}`;
                }
            }
        },
        {
            name:"Stock",
            cell: (row) => {
                if (row.product_manage_stock) {
                  return <Cell str={false} value={row.product_stock_quantity} name={"set_stock_quantity"} id={row.product_id} />;
                }
            }
        },
        
    ];
    return( 
    <>
        {stockManagerAppLocalizer.pro_active == 'free'  ?
            <div>
                <Dialog
                    className="woo-module-popup"
                    open={model}
                    onClose={handleClose}
                    aria-labelledby="form-dialog-title"
                >	
                    <span 
                        className="icon-cross stock-manager-popup-cross"
                        onClick={handleClose}
                    ></span>
                    <Popoup/>
                </Dialog>
                <img
                    src={ stockManagerAppLocalizer.manage_stock }
                    alt="Inventory-manager"
                    className='subscriber-img'
                    onClick={(e) => { 
                        CheckProActive();
                    }}
                />
                
            </div>
        
        :
            <div className="woo-subscriber-list">
                <div className="woo-container">
                    <div className="woo-middle-container-wrapper">
                        <div className="woo-search-and-multistatus-wrap">
                            <div className="woo-page-title">
                                <p>
                                Inventory Manager
                                </p>
                            </div>
                        </div>
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
}


export default Managestock;