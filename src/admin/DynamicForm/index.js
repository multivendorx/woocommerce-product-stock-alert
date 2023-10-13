/* global stockalertappLocalizer */
import React from 'react';
import Select from 'react-select';
import axios from 'axios';
import Dialog from "@mui/material/Dialog";
import Popoup from './popupcontent';

export default class DynamicForm extends React.Component {
	state = {};
	constructor(props) {
		super(props);
		this.state = {
			open_model: false,
			datamclist: [],
			from_loading: false,
			errordisplay: ''
		};
		this.handle_get_mailchimp_list = this.handle_get_mailchimp_list.bind(this);
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

	handle_get_mailchimp_list() {
		if (stockalertappLocalizer.pro_active != 'free' ) {
			axios
				.get(
					`${stockalertappLocalizer.apiUrl}/mvx_stockalert_pro/v1/get_mailchimp_list`,
				)
				.then((response) => {
					this.setState({
						datamclist: response.data,
					});
				});
		} else {
			this.setState({
				open_model: true,
			});
		}		
	}

	onSubmit = (e) => {
		// block to refresh pages
		const prop_submitbutton =
			this.props.submitbutton && this.props.submitbutton === 'false'
				? ''
				: 'true';
		if (prop_submitbutton) {
			e.preventDefault();
		}
		this.setState({ from_loading: true });

		axios({
			method: this.props.method,
			url: stockalertappLocalizer.apiUrl + '/' + this.props.url,
			data: {
				model: this.state,
				modulename: this.props.modulename,
			},
		}).then((res) => {
			this.setState({
				from_loading: false,
				errordisplay: res.data.error,
			});
			setTimeout(() => {
				this.setState({ errordisplay: '' });
			}, 2000);
			if (res.data.redirect_link) {
				window.location.href = res.data.redirect_link;
			}
		});
	};

	componentDidMount() {
		//Fetch all datas
		this.props.model.map((m) => {
			this.setState({
				[m.key]: m.database_value,
			});
		});
		if (stockalertappLocalizer.pro_active != 'free' ) {
			this.handle_get_mailchimp_list();
		}
	}

	CheckProActive = (e, key ) => {
		if (stockalertappLocalizer.pro_settings_list.includes(key)) {
			if (stockalertappLocalizer.pro_active == 'free' ) {
				this.setState({
					open_model: true,
				});
			}
		}
	}

	onChange = (e, key, type = 'single', from_type = '', array_values = []) => {
		if (!stockalertappLocalizer.pro_settings_list.includes(key)) {
			if (type === 'single') {
				if (from_type === 'select') {
					this.setState(
						{
							[key]: array_values[e.index],
						},
						() => {}
					);
				} else if (from_type === 'mailchimp_select') {
					this.setState(
						{
							[key]: array_values[e.index],
						},
						() => {}
					);
				} else if (from_type === 'multi-select') {
					this.setState(
						{
							[key]: e,
						},
						() => {}
					);
				} else if (from_type === 'text_api') {
					this.setState(
						{
							[key]: e.target.value,
						},
						() => {}
					);
					this.setState({
						datamclist: [],
					});
					this.setState({
						selected_mailchimp_list: '',
					});
					
				} else {
					this.setState(
						{
							[key]: e.target.value,
						},
						() => {}
					);
				}
			} else {
				// Array of values (e.g. checkbox): TODO: Optimization needed.
				const found = this.state[key]
					? this.state[key].find((d) => d === e.target.value)
					: false;

				if (found) {
					const data = this.state[key].filter((d) => {
						return d !== found;
					});
					this.setState({
						[key]: data,
					});
				} else {
					const others = this.state[key] ? [...this.state[key]] : [];
					this.setState({
						[key]: [e.target.value, ...others],
					});
				}
			}
			if (this.props.submitbutton && this.props.submitbutton === 'false') {
				if (key != 'password') {
					setTimeout(() => {
						this.onSubmit('');
					}, 10);
				}
			}
		} else {
			this.setState({
				open_model: true,
			});
		}
	};

	renderForm = () => {
		const model = this.props.model;
		const formUI = model.map((m, index) => {
			const key = m.key;
			const type = m.type || 'text';
			const props = m.props || {};
			const name = m.name;
			let value = m.value;
			const placeholder = m.placeholder;
			const limit = m.limit;
			let input = '';

			const target = key;
			
			value = this.state[target] || '';
			
			if (
				m.restricted_page &&
				m.restricted_page === this.props.location
			) {
				return false;
			}

			// If no array key found
			// If no array key found
			if (!m.key) {
				return false;
			}

			// for select selection
			if (
				m.depend &&
				this.state[m.depend] &&
				this.state[m.depend].value &&
				this.state[m.depend].value != m.dependvalue
			) {
				return false;
			}

			// for radio button selection
			if (
				m.depend &&
				this.state[m.depend] &&
				!this.state[m.depend].value &&
				this.state[m.depend] != m.dependvalue
			) {
				return false;
			}

			// for checkbox selection
			if (
				m.depend_checkbox &&
				this.state[m.depend_checkbox] &&
				this.state[m.depend_checkbox].length === 0
			) {
				return false;
			}

			// for checkbox selection
			if (
				m.not_depend_checkbox &&
				this.state[m.not_depend_checkbox] &&
				this.state[m.not_depend_checkbox].length > 0
			) {
				return false;
			}

			if (m.depend && !this.state[m.depend]) {
				return false;
			}

			if (type === 'text' || 'url' || 'password' || 'email' || 'number') {
				input = (
					<div className="mvx-settings-basic-input-class">
						<input
							{...props}
							className="mvx-setting-form-input"
							type={type}
							key={key}
							id={m.id}
							placeholder={placeholder}
							name={name}
							value={value}
							onChange={(e) => {
								this.onChange(e, target);
							}}
							onClick={(e) => { 
								this.CheckProActive(e, target);
							}}
						/>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'color') {
				input = (
					<div className="mvx-settings-color-picker-parent-class">
						<input
							{...props}
							className="mvx-setting-color-picker"
							type={type}
							key={key}
							id={m.id}
							name={name}
							value={value}
							onChange={(e) => {
								this.onChange(e, target);
							}}
							onClick={(e) => { 
								this.CheckProActive(e, target);
							}}
						/>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'blocktext') {
				input = (
					<div className="mvx-blocktext-class">
						{m.blocktext ? (
							<p
								className="mvx-settings-metabox-description-code"
								dangerouslySetInnerHTML={{
									__html: m.blocktext,
								}}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'textarea') {
				input = (
					<div className="mvx-setting-from-textarea">
						<textarea
							{...props}
							className={m.class ? m.class : 'mvx-form-input'}
							key={key}
							maxLength={limit}
							placeholder={placeholder}
							name={name}
							value={value}
							rows="4"
							cols="50"
							onChange={(e) => {
								this.onChange(e, target);
							}}
							onClick={(e) => { 
								this.CheckProActive(e, target);
							}}
						/>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'select') {
				const options_data = [];
				const defaultselect = [];
				input = m.options.map((o, index) => {
					if (o.selected) {
						defaultselect[index] = {
							value: o.value,
							label: o.label,
							index,
						};
					}
					options_data[index] = {
						value: o.value,
						label: o.label,
						index,
					};
				});
				input = (
					<div className="mvx-form-select-field-wrapper">
						<Select
							className={key}
							value={value ? value : ''}
							options={options_data}
							onChange={(e) => {
								this.onChange(
									e,
									m.key,
									'single',
									type,
									options_data
								);
							}}
							onClick={(e) => { 
								this.CheckProActive(e, target);
							}}
						></Select>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'mailchimp_select') {
				const options_data = [];
				const defaultselect = [];
				var selected_val = value;
				input = this.state.datamclist.map((o, index) => {
					if (o.selected) {
						defaultselect[index] = {
							value: o.value,
							label: o.label,
							index,
						};
					}
					options_data[index] = {
						value: o.value,
						label: o.label,
						index,
					};
				});
				input = (
					<div className="mvx-form-select-field-wrapper">
						<Select
							className={key}
							value={selected_val ? selected_val : ''}
							options={options_data}
							onChange={(e) => {
								this.onChange(
									e,
									m.key,
									'single',
									type,
									options_data
								);
							}}
						></Select>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'button') {
				input = (
					<div className="mvx-button">
						<input
							className="btn default-btn"
							type="button"
							value="Connect to Mailchimp"
							onClick={(e) =>
								this.handle_get_mailchimp_list()
							}
						/>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{
									__html: m.desc,
								}}
							></p>
						) : (
							''
						)}
					</div>	
				);
			}

			if (type === 'text_api') {
				input = (
					<div className="mvx-settings-basic-input-class">
						<input
							{...props}
							className="mvx-setting-form-input"
							type={type}
							key={key}
							id={m.id}
							placeholder={placeholder}
							name={name}
							value={value}
							onChange={(e) => {
								this.onChange(e, target, 'single', type,);
							}}
						/>
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'checkbox') {
				input = (
					<div
						className={
							m.right_content
								? 'mvx-checkbox-list-side-by-side'
								: m.parent_class
								? 'mvx-checkbox-list-side-by-side'
								: ''
						}
					>
						{m.select_deselect ? (
							<div
								className="mvx-select-deselect-trigger"
								onClick={(e) => {
									this.onSelectDeselectChange(e, m);
								}}
							>
								Select / Deselect All
							</div>
						) : (
							''
						)}
						{m.options.map((o) => {
							//let checked = o.value === value;
							let checked = false;
							if (value && value.length > 0) {
								checked =
									value.indexOf(o.value) > -1 ? true : false;
							}
							return (
								<div
									className={
										m.right_content
											? 'mvx-toggle-checkbox-header'
											: m.parent_class
											? m.parent_class
											: ''
									}
								>
									<React.Fragment key={'cfr' + o.key}>
										{m.right_content ? (
											<p
												className="mvx-settings-metabox-description"
												dangerouslySetInnerHTML={{
													__html: o.label,
												}}
											></p>
										) : (
											''
										)}
										<div className="mvx-toggle-checkbox-content">
											<input
												{...props}
												className={m.class}
												type={type}
												id={`mvx-toggle-switch-${o.key}`}
												key={o.key}
												name={o.name}
												checked={checked}
												value={o.value}
												onChange={(e) => {
													this.onChange(
														e,
														m.key,
														'multiple'
													);
												}}
												onClick={(e) => { 
													this.CheckProActive(e, target);
												}}
											/>
											<label
												htmlFor={`mvx-toggle-switch-${o.key}`}
											></label>
											{
											props.disabled ?
												<span className="table-content-pro-tag stock-alert-pro-tag">Pro</span> 
												: ''
											}
											
										</div>
										{m.right_content ? (
											''
										) : (
											<p
												className="mvx-settings-metabox-description"
												dangerouslySetInnerHTML={{
													__html: o.label,
												}}
											></p>
										)}
										{o.hints ? (
											<span className="dashicons dashicons-info">
												<div className="mvx-hover-tooltip">
													{o.hints}
												</div>
											</span>
										) : (
											''
										)}
									</React.Fragment>
								</div>
							);
						})}
						{m.desc ? (
							<p
								className="mvx-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			return m.type === 'section' || m.label === 'no_label' ? (
				input
			) : (
				<div key={'g' + key} className="mvx-form-group">
					<label
						className="mvx-settings-form-label"
						key={'l' + key}
						htmlFor={key}
					>
						<p dangerouslySetInnerHTML={{ __html: m.label }}></p>
					</label>
					<div className="mvx-settings-input-content">{input}</div>
				</div>
			);
		});
		return formUI;
	};


	render() {
		const prop_submitbutton =
			this.props.submitbutton && this.props.submitbutton === 'false'
				? ''
				: 'true';
		return (
			<div className="mvx-dynamic-fields-wrapper">
				<Dialog
					className="mvx-module-popup"
					open={this.state.open_model}
					onClose={this.handleClose}
					aria-labelledby="form-dialog-title"
				>	
					<span 
						className="icon-cross stock-alert-popup-cross" 
						onClick={this.handleClose}
					></span>
					<Popoup/>
				</Dialog>
					{this.state.errordisplay ? (
						<div className="mvx-notic-display-title">
							<i className="mvx-woo-stock-alert icon-success-notification"></i>
							{this.state.errordisplay}
						</div>
					) : (
						''
					)}

					<form
						className="mvx-dynamic-form"
						onSubmit={(e) => {
							this.onSubmit(e);
						}}
					>
						{this.renderForm()}

					</form>
			</div>
		);
	}
}
