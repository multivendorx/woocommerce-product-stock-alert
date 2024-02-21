import axios from 'axios';
import { __ } from '@wordpress/i18n';
import Dialog from "@mui/material/Dialog";
import React,{useEffect,useState} from 'react';
import Popoup from '../PopupContent/PopupContent';
import { Link} from 'react-router-dom';
import ProductTable from './ProductTable';

const Managestock = () => {
    const getDataUrl = `${ stockManagerAppLocalizer.apiUrl }/woo-stockmanager-pro/v1/inventory-manager`;
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
    const [ headers, setHeaders] = useState([]);
    const [ openDialog, setOpenDialog ] = useState(false);
    const [ inputChange, setInputChange ] = useState(false);
    
    useEffect(() => {
        if( stockManagerAppLocalizer.pro_active != 'free' ){
            axios({
                url: getDataUrl,
            }).then((response) => {
                let parsedData = JSON.parse(response.data);
                setData(parsedData.products);
                setHeaders(parsedData.headers);
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
            url: updateDataUrl,
            headers: { 'X-WP-Nonce' : stockManagerAppLocalizer.nonce },
            data: uploadData,
        })
    }

    function setfilter( name, value ){
        setFilter({
            ...filter,
            [name]: value,
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

    const getFilteredData = () => {
        let modifyData = Object.values(data);
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
                            <ProductTable setData={setData} pagination={true} headers={headers} products={getFilteredData()} />
                        </div>
                    </div>
                </div>
            </div>
        }
    </>
    );
};
export default Managestock;