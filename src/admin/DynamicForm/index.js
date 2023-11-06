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
			errordisplay: '',
			alert_text_color: '',
			button_background_color: '',
			button_border_color: '',
			button_text_color: '',
			button_background_color_onhover: '',
			button_border_color_onhover: '',
			button_text_color_onhover: '',
			button_font_size: '',
			button_border_radious: '',
			button_border_size: '',
		};

		this.handleOMouseEnter = this.handleOMouseEnter.bind( this );
		this.handleOMouseLeave = this.handleOMouseLeave.bind( this );
		this.handle_get_mailchimp_list = this.handle_get_mailchimp_list.bind(this);
		this.handleClose = this.handleClose.bind(this);
		this.handleOnChangedada = this.handleOnChangedada.bind(this);
		this.handle_get_button_color_state = this.handle_get_button_color_state.bind(this);
	}

	handleOMouseEnter( e ) {
		this.setState( {
			hover_on: true,
		} );
	}

	handleOMouseLeave( e ) {
		this.setState( {
			hover_on: false,
		} );
	}

	handle_get_button_color_state() {
		axios
			.get(
				`${stockalertappLocalizer.apiUrl}/woo_stockalert/v1/get_button_data`,
			)
			.then((response) => {
				this.setState({
					alert_text_color: response.data.alert_text_color,
					button_background_color: response.data.button_background_color,
					button_border_color: response.data.button_border_color,
					button_text_color: response.data.button_text_color,
					button_background_color_onhover: response.data.button_background_color_onhover,
					button_text_color_onhover: response.data.button_text_color_onhover,
					button_border_color_onhover: response.data.button_border_color_onhover,

					button_font_size: response.data.button_font_size,
					button_border_radious: response.data.button_border_radious,
					button_border_size: response.data.button_border_size,
				});
			});
	}

	handleOnChangedada( e, target ) {
		this.setState({
			alert_text_color: target === 'alert_text_color' ? e.target.value : this.state.alert_text_color,
			button_background_color: target === 'button_background_color' ? e.target.value : this.state.button_background_color,
			button_border_color: target === 'button_border_color' ? e.target.value : this.state.button_border_color,
			button_text_color: target === 'button_text_color' ? e.target.value : this.state.button_text_color,
			button_background_color_onhover: target === 'button_background_color_onhover' ? e.target.value : this.state.button_background_color_onhover,
			button_border_color_onhover: target === 'button_border_color_onhover' ? e.target.value : this.state.button_border_color_onhover,
			button_text_color_onhover: target === 'button_text_color_onhover' ? e.target.value : this.state.button_text_color_onhover,

			button_font_size: target === 'button_font_size' ? e.target.value : this.state.button_font_size,
			button_border_radious: target === 'button_border_radious' ? e.target.value : this.state.button_border_radious,
			button_border_size: target === 'button_border_size' ? e.target.value : this.state.button_border_size,
		});

		if ( this.props.submitbutton && this.props.submitbutton === 'false' ) {
			setTimeout( () => {
				this.onSubmit( '' );
			}, 10 );
		}
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
					`${stockalertappLocalizer.apiUrl}/woo_stockalert_pro/v1/get_mailchimp_list`,
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
		this.handle_get_button_color_state();
		//Fetch all datas
		this.props.model.map((m) => {
			this.setState({
				[m.key]: m.database_value,
			});
		});

		if (stockalertappLocalizer.pro_active != 'free' ) {
			this.handle_get_mailchimp_list();
		}
		
		let $ = jQuery;
		$(document).ready(function () {
			const allRanges = document.querySelectorAll(".woo-progress-picker-wrap");
			allRanges.forEach(wrap => {
				const range = wrap.querySelector("input.woo-setting-range-picker");
				const bubble = wrap.querySelector(".bubble");

				range.addEventListener("input", () => {
					setBubble(range, bubble);
				});
				setBubble(range, bubble);
			});

			function setBubble(range, bubble) {
				const max = range.max ? range.max : 100;
				bubble.style.left = range.value / max * 100 + "%";
			}

		});	
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
					<div className="woo-settings-basic-input-class">
						<input
							{...props}
							className="woo-setting-form-input"
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
								className="woo-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if (type === 'form_customize_table') {
				input = (
					<div class="editor-left side">
						<div class="left_side_wrap">
							<div className="woo-color-picker-wrap">
								Alert Text
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'alert_text_color'
										);
									} }
									value={this.state.alert_text_color}
								/>
							</div>
							<div className="woo-color-picker-wrap">
								Background
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'button_background_color'
										);
									} }
									value={this.state.button_background_color}
								/>
							</div>
							<div className="woo-color-picker-wrap">
								Border
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'button_border_color'
										);
									} }
									value={this.state.button_border_color}
								/>
							</div>
							<div className="woo-color-picker-wrap">
								Text
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'button_text_color'
										);
									} }
									value={this.state.button_text_color}
								/>
							</div>
							<div className="woo-color-picker-wrap">
								Hover Background
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'button_background_color_onhover'
										);
									} }
									value={this.state.button_background_color_onhover}
								/>
							</div>
							<div className="woo-color-picker-wrap">
								Hover Border
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'button_border_color_onhover'
										);
									} }
									value={this.state.button_border_color_onhover}
								/>
							</div>
							<div className="woo-color-picker-wrap">
								Hover Text
								<input
									{ ...props }
									className="woo-setting-color-picker"
									type="color"
									onChange={ ( e ) => {
										this.handleOnChangedada(
											e,
											'button_text_color_onhover'
										);
									} }
									value={this.state.button_text_color_onhover}
								/>
							</div>
						</div>
						<div class="right_side_wrap">
							<div className="woo-size-picker-wrap">
								Font Size
								<div className="woo-progress-picker-wrap">
									<input 
										{ ...props }
										className="woo-setting-range-picker"
										id="button_font_size"
										type="range"
										min="0"
										max="30"
										value={this.state.button_font_size}
										onChange={ ( e ) => {
											this.handleOnChangedada(
												e,
												'button_font_size'
											);
										} }
									/>
									<output class="bubble">{this.state.button_font_size}px</output>
								</div>
							</div>
							<div className="woo-size-picker-wrap">
								Border Radius
								<div className="woo-progress-picker-wrap">
									<input 
										{ ...props }
										className="woo-setting-range-picker"
										id="button_border_radious"
										type="range"
										min="0"
										max="100"
										value={this.state.button_border_radious}
										onChange={ ( e ) => {
											this.handleOnChangedada(
												e,
												'button_border_radious'
											);
										} }
									/>
									<output class="bubble">{this.state.button_border_radious}px</output>
								</div>
							</div>
							<div className="woo-size-picker-wrap">
								Border Size
								<div className="woo-progress-picker-wrap">
									<input 
										{ ...props }
										className="woo-setting-range-picker"
										id="button_border_size"
										type="range"
										min="0"
										max="10"
										value={this.state.button_border_size}
										onChange={ ( e ) => {
											this.handleOnChangedada(
												e,
												'button_border_size'
											);
										} }
									/>
									<output class="bubble">{this.state.button_border_size}px</output>
								</div>
							</div>
						</div>
					</div>			
				);
			}

			if (type === 'color') {
				input = (
					<div className="woo-settings-color-picker-parent-class">
						<input
							{...props}
							className="woo-setting-color-picker"
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
								className="woo-settings-metabox-description"
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
					<div className="woo-blocktext-class">
						{m.blocktext ? (
							<p
								className="woo-settings-metabox-description-code"
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
					<div className="woo-setting-from-textarea">
						<textarea
							{...props}
							className={m.class ? m.class : 'woo-form-input'}
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
								className="woo-settings-metabox-description"
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
					<div className="woo-form-select-field-wrapper">
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
								className="woo-settings-metabox-description"
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
					<div className="woo-form-select-field-wrapper">
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
								className="woo-settings-metabox-description"
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
					<div className="woo-button">
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
								className="woo-settings-metabox-description"
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
					<div className="woo-settings-basic-input-class">
						<input
							{...props}
							className="woo-setting-form-input"
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
								className="woo-settings-metabox-description"
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
								? 'woo-checkbox-list-side-by-side'
								: m.parent_class
								? 'woo-checkbox-list-side-by-side'
								: ''
						}
					>
						{m.select_deselect ? (
							<div
								className="woo-select-deselect-trigger"
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
											? 'woo-toggle-checkbox-header'
											: m.parent_class
											? m.parent_class
											: ''
									}
								>
									<React.Fragment key={'cfr' + o.key}>
										{m.right_content ? (
											<p
												className="woo-settings-metabox-description"
												dangerouslySetInnerHTML={{
													__html: o.label,
												}}
											></p>
										) : (
											''
										)}
										<div className="woo-toggle-checkbox-content">
											<input
												{...props}
												className={m.class}
												type={type}
												id={`woo-toggle-switch-${o.key}`}
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
												htmlFor={`woo-toggle-switch-${o.key}`}
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
												className="woo-settings-metabox-description"
												dangerouslySetInnerHTML={{
													__html: o.label,
												}}
											></p>
										)}
										{o.hints ? (
											<span className="dashicons dashicons-info">
												<div className="woo-hover-tooltip">
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
								className="woo-settings-metabox-description"
								dangerouslySetInnerHTML={{ __html: m.desc }}
							></p>
						) : (
							''
						)}
					</div>
				);
			}

			if ( type === 'example_form' ) {
				input = (
					<div className="woo-settings-example-button-class">
						{ <div class="example_form_view">
							<div class="example_form_alert_text" style={{
								color: this.state.alert_text_color
							}}>
								{this.state.alert_text ? this.state.alert_text : stockalertappLocalizer.default_alert_text}
							</div>
							<div class="example_form">
								<div class="example_form_email">
									<input type="text" value={this.state.email_placeholder_text ? this.state.email_placeholder_text : stockalertappLocalizer.default_email_place} readOnly/>
								</div>
								<div className='example_alert_button'
									onMouseEnter={ this.handleOMouseEnter }
									onMouseLeave={ this.handleOMouseLeave }
									style={ {
										color:
											this.state.hover_on && this.state.button_text_color_onhover
												? this.state.button_text_color_onhover
												: this.state.button_text_color,
										fontSize:
											this.state.button_font_size +'px',
										borderRadius:
											this.state.button_border_radious + 'px',
										border: `${ this.state.button_border_size }px solid ${ this.state.hover_on && this.state.button_border_color_onhover ? this.state.button_border_color_onhover : this.state.button_border_color }`,
										
										background: 
											this.state.hover_on && this.state.button_background_color_onhover
												? this.state.button_background_color_onhover
												: this.state.button_background_color,
										verticalAlign: 'middle',
										textDecoration: 'none',
										width: 'fit-content',
									} }
								>
									{this.state.button_text ? this.state.button_text : stockalertappLocalizer.default_alert_button}
								</div>
							</div>
						</div>
						}
					</div>
				);
				
			}

			return m.type === 'section' || m.label === 'no_label' ? (
				input
			) : (
				<div key={'g' + key} className="woo-form-group">
					<label
						className="woo-settings-form-label"
						key={'l' + key}
						htmlFor={key}
					>
						<p dangerouslySetInnerHTML={{ __html: m.label }}></p>
					</label>
					<div className="woo-settings-input-content">{input}</div>
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
			<div className="woo-dynamic-fields-wrapper">
				<Dialog
					className="woo-module-popup"
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
						<div className="woo-notic-display-title">
							<i className="woo-woo-stock-alert icon-success-notification"></i>
							{this.state.errordisplay}
						</div>
					) : (
						''
					)}

					<form
						className="woo-dynamic-form"
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
