import { render } from '@wordpress/element';
import StockAlert from "./admin/stockalert";

/**
 * Import the stylesheet for the plugin.
 */
//import './style/main.scss';
// Render the App component into the DOM
render(<StockAlert />, document.getElementById('mvx-admin-stockalert'));
