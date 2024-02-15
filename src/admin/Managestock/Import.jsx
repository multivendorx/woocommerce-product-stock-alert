import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { CSVLink } from "react-csv";
import { __ } from '@wordpress/i18n';
import { BrowserRouter as Router, Route, Link } from 'react-router-dom';

const Import = () => {
    useEffect(() => {
        if (stockManagerAppLocalizer.pro_active != 'free') {
            axios({
                url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/manage-stock`,
            }).then((response) => {
                let products = JSON.parse(response.data);
                setData(products);
            });
        }
    }, []);
    const [data, setData] = useState([]);
    const [file, setFile] = useState(null);
    const [filename, setFilename] = useState("");
    const handleFileChange = (event) => {
        setFile(event.target.files[0]);
        setFilename(event.target.files[0].name)
    };
    const header = [
        { label: 'SKU', key: 'product_sku' },
        { label: 'Manage stock', key: 'product_manage_stock' },
        { label: 'Stock status', key: 'product_stock_status' },
        { label: 'Backorders', key: 'product_backorders' },
        { label: 'Stock', key: 'product_stock_quantity' },
    ];
    const processCSV = (str, delim = ',') => {
        const headers = str.slice(0, str.indexOf('\n')).split(delim);
        const rows = str.slice(str.indexOf('\n') + 1).split('\n');
        const processedCsvData = rows.map(row => {
            const values = row.split(delim);
            const eachObject = headers.reduce((obj, header, i) => {
                obj[header] = values[i];
                return obj;
            }, {})
            return eachObject;
        })
        return processedCsvData;
    };
    const handleUpload = () => {
        if (file) {
            const reader = new FileReader();
            reader.readAsText(file);
            reader.onload = function (e) {
                let csvData = processCSV(e.target.result);
                axios({
                    method: 'post',
                    url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/import`,
                    headers: { 'Content-Type': 'application/json' },
                    data: { data: csvData }
                })
            }
        }
    };
    return (
        <div className="woo-container">
            <div className='import-page'>
                <div className="woo-page-title">
                    <p>{__('Import', 'woocommerce-stock-manager-pro')}</p>
                            <button class="import-export-btn" >
                                <Link to={'?page=woo-stock-manager-setting#&tab=manage-stock'}>
                                    <div className='wp-menu-image dashicons-before dashicons-arrow-left-alt'></div>
                                    {__("Inventory Manager", "woocommerce-stock-manager-pro")}
                                </Link>
                            </button>
                </div>
                <div className="import-section">
                    <p>{__('Upload your CSV file to update stock data for existing products. The file must match the specified format; a sample CSV is available for reference.', 'woocommerce-stock-manager-pro')}
                        <CSVLink enclosingCharacter={``} data={data} headers={header} filename={'Sample.csv'}>{__('Download Sample CSV', 'woocommerce-stock-manager-pro')}</CSVLink></p>
                    <div className='import-table'>                        
                        <div className='import-csv-section'>
                            <div className='dashicons dashicons-format-image'></div>
                            <p>{filename !== "" ? filename : "Drag your file here or click in this area"}</p>
                            <input className='import-input' onChange={handleFileChange} type="file" name="csv_file" accept=".csv" />
                        </div>
                        <div className='import-upload-btn-section'>
                            <button onClick={handleUpload} class="import-btn">{__('Upload CSV', 'woocommerce-stock-manager-pro')}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
export default Import;