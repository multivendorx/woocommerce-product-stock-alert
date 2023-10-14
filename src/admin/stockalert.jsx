import React, { Component } from 'react';
import { BrowserRouter as Router, useLocation } from 'react-router-dom';
import WOOTab from './tabs';
import Subscriber from './subscriber';

class StockAlert_Backend_Endpoints_Load extends Component {
	constructor(props) {
		super(props);
		this.state = {};
		this.Stockalert_backend_endpoint_load = this.Stockalert_backend_endpoint_load.bind(this);
	}

	useQuery() {
		return new URLSearchParams(useLocation().hash);
	}
	
	Stockalert_backend_endpoint_load() {
		// For active submneu pages
		const $ = jQuery;
		const menuRoot = $( '#toplevel_page_' + 'woo-stock-alert-setting' );

		const currentUrl = window.location.href;
		const currentPath = currentUrl.substr(
			currentUrl.indexOf( 'admin.php' )
		);
		
		menuRoot.on( 'click', 'a', function () {
			const self = $( this );
			$( 'ul.wp-submenu li', menuRoot ).removeClass( 'current' );
			if ( self.hasClass( 'wp-has-submenu' ) ) {
				$( 'li.wp-first-item', menuRoot ).addClass( 'current' );
			} else {
				self.parents( 'li' ).addClass( 'current' );
			}
		} );

		$( 'ul.wp-submenu a', menuRoot ).each( function ( index, el ) {
			if ( $( el ).attr( 'href' ) === currentPath ) {
				$( el ).parent().addClass( 'current' );
			} else {
				$( el ).parent().removeClass( 'current' );
				// if user enter page=catalog
				if (
					$( el ).parent().hasClass( 'wp-first-item' ) &&
					currentPath === 'admin.php?page=woo-stock-alert-setting'
				) {
					$( el ).parent().addClass( 'current' );
				}
			}
		} );
		const location = this.useQuery();
		if (
			location.get('tab') &&
			location.get('tab') === 'settings'
		) {
			return <WOOTab
				model='stock_alert-settings'
				query_name={location.get('tab')}
				subtab={location.get('subtab')}
				funtion_name={this}
			/>;
		} else if(
			location.get('tab') &&
			location.get('tab') === 'subscriber-list'
		) {
			return <Subscriber/>;	
		} else {
			return <WOOTab
				model='stock_alert-settings'
				query_name='settings'
				subtab='general'
				funtion_name={this}
			/>;
		}			
	}

	render() {
		return (
			<Router> 
				<this.Stockalert_backend_endpoint_load />
			</Router>
		);
	}
}
export default StockAlert_Backend_Endpoints_Load;
