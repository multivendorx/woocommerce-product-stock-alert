/* global stockManagerAppLocalizer */
import React, {useState, useEffect, useRef} from 'react';
import axios from 'axios';
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';
import TextArea from '../CustomInputs/TextArea';
import CheckBox from '../CustomInputs/CheckBox';
import BasicInput from '../CustomInputs/BasicInput';
import SelectInput from '../CustomInputs/SelectInput';
import Section from '../CustomInputs/Util/Section';
import BlockText from '../CustomInputs/Util/BlockText';
import Button from '../CustomInputs/Button';
import ColorInput from '../CustomInputs/ColorInput';
import RangeInput from '../CustomInputs/RangeInput';

const DynamicForm = ({ currentTab, tabs, setTabs }) => {
	const isFirstRender = useRef(true);
	const [ modelOpen, setModelOpen ] = useState(false);
	const [ errorDisplay, setErrorDisplay ] = useState('');
	const [ hoverOn, setHoverOn ] = useState(false);
	const [ settings, setSettings ] = useState(null);
	const [ dataMcList, setDataMcList ] = useState([]);
	
	const tabfields = tabs[currentTab].module;
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
			console.log(settings)
			console.log(currentTab)
			axios({
				method: 'post',
				url: stockManagerAppLocalizer.apiUrl + '/stockmanager/v1/' + submitUrl,
				headers: { 'X-WP-Nonce' : stockManagerAppLocalizer.nonce },
				data: {
					model: settings,
					modulename: currentTab,
				},
			}).then((res) => {
				setErrorDisplay(res.data.error);
				setTimeout(( ) => {
					setErrorDisplay('');
				}, 2000);
			});
		}
		submitData();
	}, [ settings ] );

	const handleModelClose = ( ) => {
		setModelOpen( false );
	}
	
	const checkProActive = ( e, key ) => {
		if (stockManagerAppLocalizer.pro_settings_list.includes( key )) {
			if (stockManagerAppLocalizer.pro_active == 'free' ) {
				setModelOpen(true);
			}
		}
	}

	const handleOnChange = ( e, key, type = 'single', form_type = '', array_values = [] ) => {
		if ( !stockManagerAppLocalizer.pro_settings_list.includes( key ) ) {
			if ( type === 'single' ) {
				if ( form_type === 'select' || form_type === 'mailchimp_select') {
					setSettings((preData) => {
						return { ...preData, [key]: array_values[ e.index] };
					});
				} else if ( form_type === 'multi-select' ) {
					setSettings((preData) => {
						return { ...preData, [key]: e };
					});
				} else if (form_type === 'text_api') {
					setSettings((preData) => {
						return { ...preData, [key]: e.target.value };
					});
					setDataMcList([]);
				} else if (form_type === 'checkbox') {
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
					`${stockManagerAppLocalizer.apiUrl}/stockmanager/v1/get-mailchimp-list`,
				)
				.then((response) => {
					setDataMcList(response.data);
				});
		} else {
			setModelOpen(true);
		}
	}

	const renderForm = () => {
		return tabfields.map((inputField) => {
			const key = inputField.key;
			const type = inputField.type || 'text';
			const name = inputField.name;
			const value = settings[key] || '';
			const placeholder = inputField.placeholder;
			let input = '';
			
			if (inputField.depend_checkbox) {
				if (! settings[inputField.depend_checkbox]) {
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
					input = <BasicInput
								wrapperClass={"woo-settings-basic-input-class"}
								inputClass={"woo-setting-form-input"}
								key={key}
								name={name}
								type={type}
								placeholder={placeholder}
								value={value}
								onChange={(e) => { handleOnChange(e, key, 'single', type) }}
								onClick={(e) => { checkProActive(e, key) }}
								descClass={"woo-settings-metabox-description"}
								description={ inputField.desc }
							/>
					break;
				case 'textarea':

					input = <TextArea 
								wrapperClass={"woo-setting-from-textarea"} 
								inputClass={inputField.class ? inputField.class : 'woo-form-input'}
								key={key}
								maxLength={inputField.limit}
								placeholder={placeholder}
								name={name}
								value={value}
								rowNumber={"4"}
								colNumber={"50"}
								onChange={ (e) => { handleOnChange(e, key) } }
								onClick={ (e) => { checkProActive(e, key) } }
								description={ inputField.desc }
								descClass={ "woo-settings-metabox-description" }
							/>
					break;
				case 'checkbox':
					input = (
						<div className={inputField.right_content || inputField.parent_class ? 'woo-checkbox-list-side-by-side' : ''} >
							{
								inputField.options.map(( option ) => {
									let checked = settings[option.key] ? true : false;
									return (
										<div
											className={inputField.right_content ? 'woo-toggle-checkbox-header' : inputField.parent_class || ''}
										>
											<>
												{
													inputField.right_content && <p
														className="woo-settings-metabox-description"
														dangerouslySetInnerHTML={{ __html: option.label }}
													></p>
												}
												<CheckBox
													wrapperClass={"woo-toggle-checkbox-content"}
													inputClass={inputField.class}
													type={type}
													id={`woo-toggle-switch-${option.key}`}
													key={option.key}
													name={option.name}
													checked={checked}
													value={option.value}
													onChange={(e) => { handleOnChange(e, option.key, 'single', type) }}
													onClick={(e) => { checkProActive(e, key) }}
													description={option.label}
													label={option.key}
													pro={stockManagerAppLocalizer.pro_active === 'free' &&
													stockManagerAppLocalizer.pro_settings_list.includes(key)}
													descClass={"woo-settings-metabox-description"}
												/>
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
								inputField.desc && <p
									className="woo-settings-metabox-description"
									dangerouslySetInnerHTML={{ __html: inputField.desc }}
								></p>
							}
						</div>
					);
					break;
				case 'form_customize_table':
					input = (
						<div class="editor-left side">
							<div class="left_side_wrap">
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.form_dec}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'alert_text_color') }}
									value={settings.alert_text_color}
								/>
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.submit_button_text}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'button_text_color') }}
									value={settings.button_text_color}
								/>
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.background}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'button_background_color') }}
									value={settings.button_background_color}
								/>
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.border}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'button_border_color') }}
									value={settings.button_border_color}
								/>
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.hover_background}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'button_background_color_onhover') }}
									value={settings.button_background_color_onhover}
								/>
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.hover_border}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'button_border_color_onhover') }}
									value={settings.button_border_color_onhover}
								/>
								<ColorInput
									wrapperClass={"woo-color-picker-wrap"}
									description={stockManagerAppLocalizer.setting_string.hover_text}
									inputClass={"woo-setting-color-picker"}
									onChange={(e) => { handleOnChange(e, 'button_text_color_onhover') }}
									value={settings.button_text_color_onhover}
								/>
							</div>
							<div class="right_side_wrap">
								<RangeInput
									wrapperClass={"woo-size-picker-wrap"}
									subWrapperClass={"woo-progress-picker-wrap"}
									inputClass={"woo-setting-range-picker"}
									description={stockManagerAppLocalizer.setting_string.font_size}
									id={"button_font_size"}
									min={"0"}
									max={"30"}
									value={settings.button_font_size}
									onChange={(e) => { handleOnChange(e, 'button_font_size') }}
									outputClass={"bubble"}
								/>
								<RangeInput
									wrapperClass={"woo-size-picker-wrap"}
									subWrapperClass={"woo-progress-picker-wrap"}
									inputClass={"woo-setting-range-picker"}
									description={stockManagerAppLocalizer.setting_string.border_radius}
									id={"button_border_radious"}
									min={"0"}
									max={"30"}
									value={settings.button_border_radious}
									onChange={(e) => { handleOnChange(e, 'button_border_radious') }}
									outputClass={"bubble"}
								/>
								<RangeInput
									wrapperClass={"woo-size-picker-wrap"}
									subWrapperClass={"woo-progress-picker-wrap"}
									inputClass={"woo-setting-range-picker"}
									description={stockManagerAppLocalizer.setting_string.border_size}
									id={"button_border_size"}
									min={"0"}
									max={"30"}
									value={settings.button_border_size}
									onChange={(e) => { handleOnChange(e, 'button_border_size') }}
									outputClass={"bubble"}
								/>
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
					const selectArray = type === 'select' ? inputField.option : dataMcList;
					const optionsData = [];
					selectArray.forEach((option, index) => {
						optionsData[index] = {
							value: option.value,
							label: option.label,
							index,
						};
					});
					input = <SelectInput
								wrapperClass={"woo-form-select-field-wrapper"}
								className={key}
								value={value ? value : ''}
								options={optionsData}
								onChange={(e) => { handleOnChange(e, key, 'single', type, optionsData) }}
								onClick={(e) => { checkProActive(e, key) }}
								description={ inputField.desc }
								descClass={ "woo-settings-metabox-description" }
							/>
					break;
				case 'button':
					input = <Button
								wrapperClass={"woo-button"}
								inputClass={"btn default-btn"}
								type={"button"}
								value={"Connect to Mailchimp"}
								onClick={(e) => handleGetMailchimpList()}
								descClass={"woo-settings-metabox-description"}
								description={inputField.desc}
							/>
					break;
				case 'section':
					input = <Section wrapperClass={"woo-setting-section-divider"} />
					break;
				case 'heading':
					input = (
						<div className="woo-setting-section-header">
							{
								inputField.blocktext &&
								<h5 dangerouslySetInnerHTML={{ __html: inputField.blocktext }}></h5>
							}
						</div>
					);
					break;
				case 'blocktext':
					input = < BlockText
								wrapperClass={"woo-blocktext-class"}
								blockTextClass={"woo-settings-metabox-description-code"}
								value={inputField.blocktext}
							/>
					break;
			}

			return inputField.type === 'section' || inputField.label === 'no_label' ? ( input ) : (
				<div key={'g' + key} className="woo-form-group">
					<label
						className="woo-settings-form-label"
						key={'l' + key}
						htmlFor={key}
					>
						<p dangerouslySetInnerHTML={{ __html: inputField.label }}></p>
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