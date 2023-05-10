/* global stockalertappLocalizer */
import React, { Component } from 'react';
class Banner extends Component {
	render() {
		return (
			<div className="mvx-sidebar">
				<div className="mvx-banner-right">
					<div className="mvx-logo-right">
						<a href="https://multivendorx.com/contact-us/">
							<img
								src={ stockalertappLocalizer.banner_img }
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