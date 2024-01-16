/*stockalertstockManagerAppLocalizer*/
import React, { Component } from 'react';
import axios from 'axios';
import { CSVLink } from 'react-csv';
import DataTable from 'react-data-table-component';
import PuffLoader from 'react-spinners/PuffLoader';
import { css } from '@emotion/react';
import { DateRangePicker } from 'rsuite';
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';

const override = css`
    display: block;
    margin: 0 auto;
    border-color: red;
`;

class Subscriber extends Component {
    constructor(props) {
        super(props);
        this.state = {
            subscriber_loading: false,
			subscribe_active: 'any',
			subscription_list_status_all: true,
			subscription_list_status_subscription: false,
			subscription_list_status_unsubscription: false,
			subscription_list_status_mail_sent: false,
            all_subscriber_list: [],
            data_subscriber: [],
			data_unsubscriber: [],
			data_email_sent_subscriber: [],
			data_trash_subscriber: [],
            datasubscriber: [],
            columns_subscriber_list: [],
            date_range: '',
			open_model: false,
        };
		this.onSubChange = this.onSubChange.bind(this);
        this.handle_subscription_live_search = this.handle_subscription_live_search.bind(this);
        this.handlesubscriptionsearch = this.handlesubscriptionsearch.bind(this);
		this.handleupdatesub = this.handleupdatesub.bind(this);
		this.handleClose = this.handleClose.bind(this);
    }

	handleClose() {
		this.setState({
			open_model: false,
		});
	}

	handleCloseDialog() {
		this.setState({
			open_model: false,
		});
	}

	CheckProActive = () => {
		if (stockManagerAppLocalizer.pro_active == 'free' ) {
			this.setState({
				open_model: true,
			});
		}
	}

	handleupdatesub(e) {
		this.setState({
			date_range: e,
		});

		axios
		.get(
			`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
			{
				params: { date_range: e },
			}
		).then((response) => {
			this.setState({
				datasubscriber: response.data,
			});
		});
	}

	onSubChange(e, name) {
       
    }

    handlesubscriptionsearch(e, status) {
		 if (status === 'searchproduct') {
			if (e && e.target.value.length > 2) {
				axios
					.get(
						`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/search_subscribe_by_product`,
						{
							params: { product: e.target.value, subscription_status: this.state.subscribe_active, date_range: this.state.date_range },
						}
					)
					.then((response) => {
						this.setState({
							datasubscriber: response.data,
						});
					});
			} else {
				axios
				.get(
					`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
					{
						params: { date_range: this.state.date_range, subscription_status: this.state.subscribe_active },
					}
				).then((response) => {
						this.setState({
							datasubscriber: response.data,
						});
					});
			}
		}
	}

    handle_subscription_live_search(e) {
		if (e.target.value) {
			axios
				.get(
					`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/search_specific_subscribe`,
					{
						params: { email_id: e.target.value },
					}
				)
				.then((response) => {
					this.setState({
						datasubscriber: response.data,
					});
				});
		} else {
			axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
				this.setState({
					datasubscriber: response.data,
				});
			});
		}
	}


    handle_subscription_status_check(e, type) {
		if (type === 'subscribe') {

			this.setState({
				subscribe_active: 'woo_subscribed',
				subscription_list_status_all: false,
				subscription_list_status_subscription: true,
				subscription_list_status_unsubscription: false,
				subscription_list_status_mail_sent: false,
			});
			// subscribe status
			axios
				.get(
					`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
					{
						params: { subscription_status: 'woo_subscribed' },
					}
				)
				.then((response) => {
					this.setState({
						datasubscriber: response.data
					});
				});
		}

		if (type === 'unsubscribe') {
			// unsubscribe status
			this.setState({
				subscribe_active: 'woo_unsubscribed',
				subscription_list_status_all: false,
				subscription_list_status_subscription: false,
				subscription_list_status_unsubscription: true,
				subscription_list_status_mail_sent: false,
			});
			axios
				.get(
					`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
					{
						params: { subscription_status: 'woo_unsubscribed' },
					}
				)
				.then((response) => {
					this.setState({
						datasubscriber: response.data,
					});
				});
		}

		if (type === 'mail_sent') {
			// refunded status
			this.setState({
				subscribe_active: 'woo_mailsent',
				subscription_list_status_all: false,
				subscription_list_status_subscription: false,
				subscription_list_status_unsubscription: false,
				subscription_list_status_mail_sent: true,
			});
			axios
				.get(
					`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
					{
						params: { subscription_status: 'woo_mailsent' },
					}
				)
				.then((response) => {
                    console.log(response.data);
					this.setState({
						datasubscriber: response.data,
					});
				});
		}
		
		if (type === 'all') {
			this.setState({
				subscribe_active: 'any',
				subscription_list_status_all: true,
				subscription_list_status_subscription: false,
				subscription_list_status_unsubscription: false,
				subscription_list_status_mail_sent: false,
			});

			axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
				this.setState({
					datasubscriber: response.data,
				});
			});
		}
	}

    common_funtions = (e) => {
		// subscribe status
		axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/no_of_subscribe_list`,
				{
					params: { subscribtion_status: 'woo_subscribed', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_subscriber: response.data,
				});
			});

		// unsubscribe status
		axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/no_of_subscribe_list`,
				{
					params: { subscribtion_status: 'woo_unsubscribed', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_unsubscriber: response.data,
				});
			});

		// mail sent status
		axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/no_of_subscribe_list`,
				{
					params: { subscribtion_status: 'woo_mailsent', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_email_sent_subscriber: response.data,
				});
			});

		// trash status
		axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/no_of_subscribe_list`,
				{
					params: { subscribtion_status: 'trash', date_range: this.state.date_range },
				}
			)
			.then((response) => {
				this.setState({
					data_trash_subscriber: response.data,
				});
			});
	};

    componentDidMount() {
		if (stockManagerAppLocalizer.pro_active != 'free' ) {
			this.common_funtions('');
			axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo_stockmanager_pro/v1/show_subscribe_from_status_list`,
				{
					params: { date_range: this.state.date_range },
				}
			).then((response) => {
				this.setState({
					datasubscriber: response.data,
					all_subscriber_list: response.data,
					subscriber_loading: true,
				});
			});

			stockManagerAppLocalizer.columns_subscriber.map((data_sub, index_sub) => {
				let data_selector_sub = '';
				let set_for_dynamic_column_sub = '';
				data_selector_sub = data_sub.selector_choice;
				data_sub.selector = (row) => (
					<div
						dangerouslySetInnerHTML={{
							__html: row[data_selector_sub],
						}}
					></div>
				);

				this.state.columns_subscriber_list[index_sub] =
					data_sub;
				set_for_dynamic_column_sub =
					this.state.columns_subscriber_list;
				this.state.columns_subscriber_list =
					set_for_dynamic_column_sub;
			});
		}
    }

    render() {
        return (
			<div>
				{ stockManagerAppLocalizer.pro_active == 'free'  ?
					<div>
						<Dialog
							className="woo-module-popup"
							open={this.state.open_model}
							onClose={this.handleClose}
							aria-labelledby="form-dialog-title"
						>	
							<span 
								className="icon-cross stock-manager-popup-cross"
								onClick={this.handleClose}
							></span>
							<Popoup/>
						</Dialog>
						<img
							src={ stockManagerAppLocalizer.subscriber_list }
							alt="subscriber-list"
							className='subscriber-img'
							onClick={(e) => { 
								this.CheckProActive();
							}}
						/>
						
					</div>
				:
					<div className="woo-subscriber-list">
						<div className="woo-container">
								<div className="woo-middle-container-wrapper">
									<div className="woo-page-title">
										<p>
										Subscriber List
										</p>
										<div className="pull-right">
											<CSVLink
												data={this.state.datasubscriber}
												headers={stockManagerAppLocalizer.columns_subscriber_list}
												filename={'Subscribers.csv'}
												className="woo-btn btn-purple"
											>
												<i className="woo-font icon-download"></i>
												{
													stockManagerAppLocalizer.download_csv
												}
											</CSVLink>
										</div>
									</div>
									<div className="woo-search-and-multistatus-wrap">
										<ul className="woo-multistatus-ul">
											<li className={`woo-multistatus-item ${this.state.subscription_list_status_all ? 'status-active' : ''}`}>
												<div
													className="woo-multistatus-check-all status-active"
													onClick={(e) =>
														this.handle_subscription_status_check(
															e,
															'all'
														)
													}
												>
													{
														stockManagerAppLocalizer
															.subscription_page_string.all
													}{' '}
													(
													{
														this.state
															.all_subscriber_list
															.length
													}
													)
												</div>
											</li>
											<li className="woo-multistatus-item woo-divider"></li>
											<li className={`woo-multistatus-item ${this.state.subscription_list_status_subscription ? 'status-active' : ''}`}>
												<div
													className="woo-multistatus-check-subscribe"
													onClick={(e) =>
														this.handle_subscription_status_check(
															e,
															'subscribe'
														)
													}
												>
													{
														stockManagerAppLocalizer
															.subscription_page_string.subscribe
													}{' '}
													(
													{
														this.state.data_subscriber
															
													}
													)
												</div>
											</li>
											<li className="woo-multistatus-item woo-divider"></li>
											<li className={`woo-multistatus-item ${this.state.subscription_list_status_unsubscription ? 'status-active' : ''}`}>
												<div
													className="woo-multistatus-check-unpaid"
													onClick={(e) =>
														this.handle_subscription_status_check(
															e,
															'unsubscribe'
														)
													}
												>
													{
														stockManagerAppLocalizer
															.subscription_page_string
															.unsubscribe
													}{' '}
													(
													{
														this.state
															.data_unsubscriber
															
													}
													)
												</div>
											</li>
											<li className="woo-multistatus-item woo-divider"></li>
											<li className={`woo-multistatus-item ${this.state.subscription_list_status_mail_sent ? 'status-active' : ''}`}>
												<div
													className="woo-multistatus-check-unpaid"
													onClick={(e) =>
														this.handle_subscription_status_check(
															e,
															'mail_sent'
														)
													}
												>
													{
														stockManagerAppLocalizer
															.subscription_page_string
															.mail_sent
													}{' '}
													(
													{
														this.state
															.data_email_sent_subscriber
															
													}
													)
												</div>
											</li>
										</ul>

										<div className="woo-header-search-section">
											<label>
												<i className="woo-font icon-search"></i>
											</label>
											<input
												type="text"
												placeholder={
													stockManagerAppLocalizer
													.subscription_page_string
													.search
												}
												onChange={
													this.handle_subscription_live_search
												}
											/>
										</div>
									</div>

									<div className="woo-wrap-bulk-all-date">
										<div className="woo-header-search-section">
											<input
												type="text"
												placeholder={
													stockManagerAppLocalizer.subscription_page_string
														.show_product
												}
												onChange={(e) =>
													this.handlesubscriptionsearch(
														e,
														'searchproduct'
													)
												}
											/>
										</div>
										
										<DateRangePicker
											placeholder={stockManagerAppLocalizer.subscription_page_string.daterenge}
											onChange={(e) => this.handleupdatesub(e)}
										/>
									</div>

									{this.state.columns_subscriber_list &&
									this.state.columns_subscriber_list.length > 0 &&
									this.state.subscriber_loading ? (
										<div className="woo-backend-datatable-wrapper">
											<DataTable
												columns={
													this.state.columns_subscriber_list
												}
												data={this.state.datasubscriber}
												selectableRows
												pagination
											/>
										</div>
									) : (
										<PuffLoader
											css={override}
											color={'#cd0000'}
											size={200}
											loading={true}
										/>
									)}
								</div>
							</div>
					</div>
				};
			</div>
        );
    }
}
export default Subscriber;