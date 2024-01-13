/* global stockManagerAppLocalizer */
import React, {useState, useEffect, useRef} from 'react';
import Select from 'react-select';
import axios from 'axios';
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';

const DynamicForm = ({ currentTab, tabs, setTabs }) => {
	const isFirstRender = useRef(true);
	const [modelOpen, setModelOpen] = useState(false);
	const [errorDisplay, setErrorDisplay] = useState('');
	const [hoverOn, setHoverOn] = useState(false);
	const [settings, setSettings] = useState(null);
	const [dataMcList, setDataMcList] = useState(null);
	
	const tabfilds = tabs[currentTab].module;
	const submitUrl = tabs[currentTab].apiurl;

	useEffect(() => {
		setSettings(tabs[currentTab].databases_value ? tabs[currentTab].databases_value :{});
		isFirstRender.current = true;
	}, [currentTab]);

	useEffect(() => {
		if (!settings) return;
		if (isFirstRender.current) {
			isFirstRender.current = false;
			return;
		}
		const submitData = () => {
			setTabs((preData) => {
				return { ...preData, [currentTab]: { ...preData[currentTab], databases_value: settings } }
			});
			axios({
				method: 'post',
				url: stockManagerAppLocalizer.apiUrl + '/woo-stockmanager/v1/' + submitUrl,
				data: {
					model: settings,
					modulename: currentTab,
				},
			}).then((res) => {
				setErrorDisplay(res.data.error);
				setTimeout(() => {
					setErrorDisplay('');
				}, 2000);
			});
		}
		submitData();
	}, [settings]);

	const handleModelClose = () => {
		setModelOpen(false);
	}
	
	const checkProActive = (e, key) => {
		if (stockManagerAppLocalizer.pro_settings_list.includes(key)) {
			if (stockManagerAppLocalizer.pro_active == 'free' ) {
				setModelOpen(true);
			}
		}
	}

	const handleOnChange = (e, key, type = 'single', from_type = '', array_values = []) => {
		if (!stockManagerAppLocalizer.pro_settings_list.includes(key)) {
			if (type === 'single') {
				if ( from_type === 'select' ) {
					setSettings((preData) => {
						return { ...preData, [key]: array_values[e.index] };
					});
				} else if ( from_type === 'multi-select' ) {
					setSettings((preData) => {
						return { ...preData, [key]: e };
					});
				} else if (from_type === 'text_api') {
					setSettings((preData) => {
						return { ...preData, [key]: e.target.value };
					});
					setDataMcList([]);
				} else if (from_type === 'checkbox') {
					setSettings((preData) => {
						return { ...preData, [key]: e.target.checked };
					});
				} else {
					if (stockManagerAppLocalizer.default_massages_fields.includes(key)) {
						if (e.target.value.length < 1) {
							setSettings((preData) => {
								return { ...preData, [key]: stockManagerAppLocalizer.default_massages[key] ? stockManagerAppLocalizer.default_massages[key] : '' };
							});
						} else {
							setSettings((preData) => {
								return { ...preData, [key]: e.target.value };
							});
						}
					} else {
						setSettings((preData) => {
							return { ...preData, [key]: e.target.value };
						});
					}
				}
			}
		} else {
			setModelOpen(true);
		}
	}

	const handleGetMailchimpList = () => {
		if (stockManagerAppLocalizer.pro_active != 'free' ) {
			axios
				.get(
					`${stockManagerAppLocalizer.apiUrl}/woo_stockalert_pro/v1/get_mailchimp_list`,
				)
				.then((response) => {
					setDataMcList(response.data);
				});
		} else {
			setModelOpen(true);
		}
	}

	const renderForm = () => {
		return tabfilds.map((inputFild) => {
			const key = inputFild.key;
			const type = inputFild.type || 'text';
			const name = inputFild.name;
			const value = settings[key] || '';
			const placeholder = inputFild.placeholder;
			let input = '';
			
			if (inputFild.depend_checkbox) {
				if (! settings[inputFild.depend_checkbox]) {
					return;
				}
			}

			switch (type) {
				case 'text':
				case 'email':
				case 'password':
				case 'number':
				case 'url':
				case 'text_api':
					input = (
						<div className="woo-settings-basic-input-class">
							<input
								className="woo-setting-form-input"
								key={key}
								name={name}
								type={type}
								placeholder={placeholder}
								value={value}
								onChange={(e) => { handleOnChange(e, key, 'single', type) }}
								onClick={(e) => { checkProActive(e, key) }}
							/>
							{
								inputFild.desc &&
								<p
									className="woo-settings-metabox-description"
									dangerouslySetInnerHTML={{ __html: inputFild.desc }}
								></p>
							}
						</div>
					);
					break;
				case 'textarea':
					input = (
						<div className="woo-setting-from-textarea">
							<textarea
								className={inputFild.class ? inputFild.class : 'woo-form-input'}
								key={key}
								maxLength={inputFild.limit}
								placeholder={placeholder}
								name={name}
								value={value}
								rows="4"
								cols="50"
								onChange={(e) => { handleOnChange(e, key) }}
								onClick={(e) => { checkProActive(e, key) }}
							/>
							{
								inputFild.desc && <p
									className="woo-settings-metabox-description"
									dangerouslySetInnerHTML={{ __html: inputFild.desc }}
								></p>
							}
						</div>
					);
					break;
				case 'checkbox':
					input = (
						<div className={inputFild.right_content || inputFild.parent_class ? 'woo-checkbox-list-side-by-side' : ''} >
							{
								inputFild.options.map((option) => {
									let checked = settings[option.key] ? true : false;
									return (
										<div
											className={inputFild.right_content ? 'woo-toggle-checkbox-header' : inputFild.parent_class || ''}
										>
											<>
												{
													inputFild.right_content && <p
														className="woo-settings-metabox-description"
														dangerouslySetInnerHTML={{ __html: option.label }}
													></p>
												}
												<div className="woo-toggle-checkbox-content">
													<input
														className={inputFild.class}
														type={type}
														id={`woo-toggle-switch-${option.key}`}
														key={option.key}
														name={option.name}
														checked={checked}
														value={option.value}
														onChange={(e) => { handleOnChange(e, option.key, 'single', type) }}
														onClick={(e) => { checkProActive(e, key) }}
													/>
													<label htmlFor={`woo-toggle-switch-${option.key}`} ></label>
													{
														stockManagerAppLocalizer.pro_active === 'free' &&
														stockManagerAppLocalizer.pro_settings_list.includes(key) &&
														<span className="table-content-pro-tag stock-manager-pro-tag">Pro</span>
													}
												</div>
												{
													(!inputFild.right_content) && <p
														className="woo-settings-metabox-description"
														dangerouslySetInnerHTML={{ __html: option.label }}
													></p>
												}
												{
													option.hints &&
													<span className="dashicons dashicons-info">
														<div className="woo-hover-tooltip">
															{option.hints}
														</div>
													</span>
												}
											</>
										</div>
									);
								})
							}
							{
								inputFild.desc && <p
									className="woo-settings-metabox-description"
									dangerouslySetInnerHTML={{ __html: inputFild.desc }}
								></p>
							}
						</div>
					);
					break;
				case 'form_customize_table':
					input = (
						<div class="editor-left side">
							<div class="left_side_wrap">
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.form_dec}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'alert_text_color') }}
										value={settings.alert_text_color}
									/>
								</div>
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.submit_button_text}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'button_text_color') }}
										value={settings.button_text_color}
									/>
								</div>
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.background}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'button_background_color') }}
										value={settings.button_background_color}
									/>
								</div>
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.border}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'button_border_color') }}
										value={settings.button_border_color}
									/>
								</div>
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.hover_background}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'button_background_color_onhover') }}
										value={settings.button_background_color_onhover}
									/>
								</div>
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.hover_border}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'button_border_color_onhover') }}
										value={settings.button_border_color_onhover}
									/>
								</div>
								<div className="woo-color-picker-wrap">
									{stockManagerAppLocalizer.setting_string.hover_text}
									<input
										className="woo-setting-color-picker"
										type="color"
										onChange={(e) => { handleOnChange(e, 'button_text_color_onhover') }}
										value={settings.button_text_color_onhover}
									/>
								</div>
							</div>
							<div class="right_side_wrap">
								<div className="woo-size-picker-wrap">
									{stockManagerAppLocalizer.setting_string.font_size}
									<div className="woo-progress-picker-wrap">
										<input
											className="woo-setting-range-picker"
											id="button_font_size"
											type="range"
											min="0"
											max="30"
											value={settings.button_font_size}
											onChange={(e) => { handleOnChange(e, 'button_font_size') }}
										/>
										<output class="bubble">{settings.button_font_size ? settings.button_font_size : 0}px</output>
									</div>
								</div>
								<div className="woo-size-picker-wrap">
									{stockManagerAppLocalizer.setting_string.border_radius}
									<div className="woo-progress-picker-wrap">
										<input
											className="woo-setting-range-picker"
											id="button_border_radious"
											type="range"
											min="0"
											max="100"
											value={settings.button_border_radious}
											onChange={(e) => { handleOnChange(e, 'button_border_radious') }}
										/>
										<output class="bubble">{settings.button_border_radious ? settings.button_border_radious : 0}px</output>
									</div>
								</div>
								<div className="woo-size-picker-wrap">
									{stockManagerAppLocalizer.setting_string.border_size}
									<div className="woo-progress-picker-wrap">
										<input
											className="woo-setting-range-picker"
											id="button_border_size"
											type="range"
											min="0"
											max="10"
											value={settings.button_border_size}
											onChange={(e) => { handleOnChange(e, 'button_border_size') }}
										/>
										<output class="bubble">{settings.button_border_size ? settings.button_border_size : 0}px</output>
									</div>
								</div>
							</div>
						</div>
					);
					break;
				case 'example_form':
					input = (
						<div className="woo-settings-example-button-class">
							{
								<div class="example_form_view">
									<div class="example_form_alert_text" style={{ color: settings.alert_text_color }}>
										{settings.alert_text ? settings.alert_text : stockManagerAppLocalizer.default_alert_text}
									</div>
									<div class="example_form">
										<div class="example_form_email">
											<input type="text" value={settings.email_placeholder_text ? settings.email_placeholder_text : stockManagerAppLocalizer.default_email_place} readOnly />
										</div>
										<div className='example_alert_button'
											onMouseEnter={(e) => { setHoverOn(true) }}
											onMouseLeave={(e) => { setHoverOn(false) }}
											style={{
												color:
													hoverOn && settings.button_text_color_onhover
														? settings.button_text_color_onhover
														: settings.button_text_color,
												fontSize: settings.button_font_size + 'px',
												borderRadius: settings.button_border_radious + 'px',
												border: `${settings.button_border_size}px solid ${hoverOn && settings.button_border_color_onhover ? settings.button_border_color_onhover : settings.button_border_color}`,
												background:
													hoverOn && settings.button_background_color_onhover
														? settings.button_background_color_onhover
														: settings.button_background_color,
												verticalAlign: 'middle',
												textDecoration: 'none',
												width: 'fit-content',
											}}
										>
											{settings.button_text ? settings.button_text : stockManagerAppLocalizer.default_alert_button}
										</div>
									</div>
								</div>
							}
						</div>
					);
					break;
				case 'select':
				case 'mailchimp_select':
					const optionsData = [];
					inputFild.options.forEach((option, index) => {
						optionsData[index] = {
							value: option.value,
							label: option.label,
							index,
						};
					});
					input = (
						<div className="woo-form-select-field-wrapper">
							<Select
								className={key}
								value={value ? value : ''}
								options={optionsData}
								onChange={(e) => { handleOnChange(e, key, 'single', type, optionsData) }}
								onClick={(e) => { checkProActive(e, key) }}
							></Select>
							{
								inputFild.desc && <p
									className="woo-settings-metabox-description"
									dangerouslySetInnerHTML={{ __html: inputFild.desc }}
								></p>
							}
						</div>
					);
					break;
				case 'button':
					input = (
						<div className="woo-button">
							<input
								className="btn default-btn"
								type="button"
								value="Connect to Mailchimp"
								onClick={(e) => handleGetMailchimpList()}
							/>
							{
								inputFild.desc && <p
									className="woo-settings-metabox-description"
									dangerouslySetInnerHTML={{ __html: inputFild.desc }}
								></p>
							}
						</div>
					);
					break;
				case 'section':
					input = (
						<div className="woo-setting-section-divider">&nbsp;</div>
					);
					break;
				case 'heading':
					input = (
						<div className="woo-setting-section-header">
							{
								inputFild.blocktext &&
								<h5 dangerouslySetInnerHTML={{ __html: inputFild.blocktext }} ></h5>
							}
						</div>
					);
					break;
				case 'blocktext':
					input = (
						<div className="woo-blocktext-class">
							{
								inputFild.blocktext && <p
									className="woo-settings-metabox-description-code"
									dangerouslySetInnerHTML={{ __html: inputFild.blocktext }}
								></p>
							}
						</div>
					);
					break;
			}

			return inputFild.type === 'section' || inputFild.label === 'no_label' ? ( input ) : (
				<div key={'g' + key} className="woo-form-group">
					<label
						className="woo-settings-form-label"
						key={'l' + key}
						htmlFor={key}
					>
						<p dangerouslySetInnerHTML={{ __html: inputFild.label }}></p>
					</label>
					<div className="woo-settings-input-content">{input}</div>
				</div>
			);
		});
	}

    return (
		<>
			{
				settings &&
				<div className="woo-dynamic-fields-wrapper">
					<Dialog
						className="woo-module-popup"
						open={modelOpen}
						onClose={handleModelClose}
						aria-labelledby="form-dialog-title"
					>
						<span
							className="icon-cross"
							onClick={handleModelClose}
						></span>
						<Popoup />
					</Dialog>
					{
						errorDisplay &&
						<div className="woo-notic-display-title">
							<i className="icon-success-notification"></i>
							{errorDisplay}
						</div>
					}
					<form
						className="woo-dynamic-form"
						onSubmit={(e) => { e.preventDefault() }}
					>
						{renderForm()}
					</form>
				</div>
			}
        </>
    );
}

export default DynamicForm;