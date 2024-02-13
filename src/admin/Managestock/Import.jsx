import React,{useState} from 'react';
import axios from 'axios';
import { __ } from '@wordpress/i18n';

const Import = () =>{
    const [file, setFile] = useState(null);
    const handleFileChange = (event) => {
        setFile(event.target.files[0]);
    };
    const processCSV = (str, delim=',') => {
        const headers = str.slice(0,str.indexOf('\n')).split(delim);
        const rows = str.slice(str.indexOf('\n')+1).split('\n');
        const processedCsvData = rows.map( row => {
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
            reader.onload = function(e) {
                let csvData = processCSV(e.target.result);
                axios({
                    method: 'post',
                    url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/import`,
                    headers: {'Content-Type': 'application/json'},
                    data:{data:csvData}
                })
            }
        }
    };
    return(
        <div className="woo-container">
            <div className="woo-page-title">
                <p>{__('Import','woocommerce-stock-manager-pro')}</p>
            </div>
            <div className="import-section">
                <h2>{__('Import','woocommerce-stock-manager-pro')}</h2>
                <p>{__('Upload your csv file with the stock data of existing product','woocommerce-stock-manager-pro')}</p>
                <p>{__('CSV file must be in this format or you can export file and edit them in this format','woocommerce-stock-manager-pro')}</p>
                <h2>{__('File Format','woocommerce-stock-manager-pro')}</h2>
                <table>
                    <thead>
                        <th>{__('SKU','woocommerce-stock-manager-pro')} </th>
                        <th>{__('Manage Stock','woocommerce-stock-manager-pro')}</th>
                        <th>{__('Stock Status','woocommerce-stock-manager-pro')}</th>
                        <th>{__('Backorders','woocommerce-stock-manager-pro')} </th>
                        <th>{__('Stock Quantity','woocommerce-stock-manager-pro')}</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{__('PKG1','woocommerce-stock-manager-pro')}</td>
                            <td>{__('yes','woocommerce-stock-manager-pro')}</td>
                            <td>{__('instock','woocommerce-stock-manager-pro')}</td>
                            <td>{__('yes','woocommerce-stock-manager-pro')}</td>
                            <td>{__('10','woocommerce-stock-manager-pro')}</td>
                        </tr>
                    </tbody>
                </table>
                    <input onChange={handleFileChange} type="file" name="csv_file" accept=".csv"/>
                    <button onClick={handleUpload} class="import-export-btn">{__('Import CSV','woocommerce-stock-manager-pro')}</button>
            </div>
        </div>
    )
}
export default Import;