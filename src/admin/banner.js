/* global stockalertappLocalizer */
import React, { Component } from 'react';
class Banner extends Component {
	render() {
		return (
			<div className="woo-sidebar">
				<div className="woo-banner-right">
					<div className="woo-logo-right">
						<a href={stockalertappLocalizer.pro_url}>
							<img
								src={stockalertappLocalizer.banner_img}
								alt="right-banner"
							/>
						</a>
					</div>
				</div>
			</div>
		);
	}
}
export default Banner;