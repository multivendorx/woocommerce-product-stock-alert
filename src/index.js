import { render } from '@wordpress/element';
import { BrowserRouter} from 'react-router-dom';
import StockAlert from './admin/Stockalert.js';

/**
 * Import the stylesheet for the plugin.
 */
import './style/main.scss';
import './style/StockManager.scss';
// Render the App component into the DOM
render(<BrowserRouter><StockAlert /></BrowserRouter>, document.getElementById('woo-admin-stockmanager'));
