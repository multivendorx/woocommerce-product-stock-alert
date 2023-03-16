/* global stockalertappLocalizer */
import React, { Component } from 'react';
import { BrowserRouter as Router, Link } from 'react-router-dom';
import axios from 'axios';
import DynamicForm from './DynamicForm';
import PuffLoader from 'react-spinners/PuffLoader';
import { css } from '@emotion/react';

const override = css`
	display: block;
	margin: 0 auto;
	border-color: red;
`;

export default class TabSection extends Component {
	state = {};
	constructor(props) {
		super(props);
		this.state = {
			fetch_admin_tabs: [],
			current: {},
			current_url: '',
		};
		console.log(this.props.subtab);
	}
	
	renderTab = () => {
		const horizontally = this.props.horizontally;
		const query_name = this.props.query_name;
		
		if (this.props.subtab !== this.state.current_url) {
			axios({
				url: `${stockalertappLocalizer.apiUrl}/mvx_stockalert/v1/fetch_admin_tabs`,
			}).then((response) => {
				this.setState({
					fetch_admin_tabs: response.data ? response.data[this.props.model] : [],
					current_url: this.props.subtab
				});
			});
		}
		const model = this.state.fetch_admin_tabs ? this.state.fetch_admin_tabs : [];
		const TabUI = Object.entries(model).length > 0 ? Object.entries(model).map((m, index) => {
			return this.props.subtab === m[0] ? (
				<div className="mvx-tab-description-start">
					<div className="mvx-tab-name">{m[1].tablabel}</div>
					<p>{m[1].description}</p>
				</div>
			) : (
				''
			);
		}) : '';
		const TabUIContent = (
			<div className={`mvx-general-wrapper mvx-${this.props.subtab}`}>
				<div className="mvx-container">
					<div
						className={`mvx-middle-container-wrapper ${
							horizontally
								? 'mvx-horizontal-tabs'
								: 'mvx-vertical-tabs'
						}`}
					>
						{this.props.tab_description &&
						this.props.tab_description === 'no'
							? ''
							: TabUI}
						<div className="mvx-middle-child-container">
							{this.props.no_tabs ? (
								''
							) : (
								<div className="mvx-current-tab-lists">
									{Object.entries(model).length > 0 ? Object.entries(model).map((m, index) => {
										return m[1].link ? (
											
												<a className={m[1].class} href={m[1].link}>
													{m[1].icon ? (
														<i
															className={`stock-alert ${m[1].icon}`}
														></i>
													) : (
														''
													)}
													{m[1].tablabel}
												</a>
											
										) : (
											
												<Link

													className={
													this.props.subtab ===
													m[0]
														? 'active-current-tab'
														: ''
												}

													to={
														`?page=woo-product-stock-alert-setting-admin#&tab=${query_name}&subtab=${m[0]}`
													}
												>
													{m[1].icon ? (
														<i
															className={`stock-alert ${m[1].icon}`}
														></i>
													) : (
														''
													)}
													{m[1].tablabel}
												</Link>
											
										);
									}) : ''}
								</div>
							)}
							<div className="mvx-tab-content">
								{
									model && Object.entries(model).length > 0 && this.props.subtab === this.state.current_url ? Object.entries(model).map((m, index) => (
										
								m[0] === this.props.subtab && m[1].modulename && m[1].modulename.length > 0 ? 
								 <DynamicForm
									key={`dynamic-form-${m[0]}`}
									title={m[1].tablabel}
									defaultValues={this.state.current}
									model={
										m[1].modulename
									}
									method="post"
									modulename={m[0]}
									url={`mvx_stockalert/v1/${m[1].apiurl}`}
									submitbutton="false"
								/>

								: (
									''
								)
								)) : 

								<PuffLoader
									css={override}
									color={'#cd0000'}
									size={200}
									loading={true}
								/>

								}
							</div>
						</div>
					</div>
				</div>
			</div>
		);
		return TabUIContent;
	};

	render() {
		return this.renderTab();
	}
}
