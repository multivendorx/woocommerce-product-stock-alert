import React, { useState } from 'react';
import axios from 'axios';

const ImportExport = (props) => {
    const [file, setFile] = useState(null);

    const processCSV = (str, delim=',') => {
        const headers = str.slice(0,str.indexOf('\n')).split(delim);
        const rows = str.slice(str.indexOf('\n')+1).split('\n');
        const newArray = rows.map( row => {
            const values = row.split(delim);
            const eachObject = headers.reduce((obj, header, i) => {
                obj[header] = values[i];
                return obj;
            }, {})
            return eachObject;
        })
        return newArray
    };
    const handleFileChange = (event) => {
        setFile(event.target.files[0]);
    };
    const handleUpload = ()=> {
        if (file) {
            const reader = new FileReader();    
            reader.readAsText(file);
            reader.onload = function(e) {
                let text = e.target.result;
                let data = processCSV(text);
                axios({
                    method: 'post',
                    url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager-pro/v1/import`,
                    data: data
                }).then((response)=>{
                    console.log(response);
                })
            }
        }
    };
    return(
        <div className="woo-container">
            <h1>Import/Export</h1>
            <div className="import">
                <h2>Import</h2>
                <p><b>Upload your csv file with the stock data</b></p>
                <p>CSV file must be in this format or you can export file and edit them in this format</p>
                <h2>File Format</h2>
                <table>
                    <th>
                        <td>SKU</td>
                        <td>manage Stock</td>
                        <td>Stock Status</td>
                        <td>Backorders</td>
                        <td>stock</td>
                        <td>Product Type</td>
                    </th>
                    <tr>
                        <td>PKG1</td>
                        <td>yes</td>
                        <td>instock</td>
                        <td>yes</td>
                        <td>10</td>
                        <td>simple</td>
                    </tr>
                </table>
                    <input onChange={handleFileChange} type="file" name="csv_file" accept=".csv"/>
                    <button onClick={handleUpload} class="import-export-btn">Upload CSV</button>
            </div>
            <div className="export">
                <h2>Export</h2>
                <p>You can download csv file,with stock data.<br />Please Select the field of which you want to download the csv.</p>
                <input type="checkbox" id="Id" />
                <label htmlFor="Id">Id</label>
                <input type="checkbox" id="Type" />
                <label htmlFor="Type">Type</label>
                <input type="checkbox" id="SKU" />
                <label htmlFor="SKU">SKU</label>
                <input type="checkbox" id="Name" />
                <label htmlFor="Name">Name</label>
                <input type="checkbox" id="Manage Stock" />
                <label htmlFor="Manage Stock">Manage Stock</label>
                <input type="checkbox" id="Stock status" />
                <label htmlFor="Stock status">Stock status</label>
                <input type="checkbox" id="Backorders" />
                <label htmlFor="Backorders">Backorders</label>
                <input type="checkbox" id="Stock Quantity" />
                <label htmlFor="Stock Quantity">Stock Quantity</label>                
            </div>
            <button class="import-export-btn" >Export CSV</button>
        </div>
    );
};
export default ImportExport;