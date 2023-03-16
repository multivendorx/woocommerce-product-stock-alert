/* global stockalertappLocalizer */
import React from 'react';
import axios from 'axios';

export default class DynamicForm extends React.Component {
	state = {};
	constructor(props) {
		super(props);
		this.state = {
			from_loading: false,
			errordisplay: ''
		};
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
				vendor_id: this.props.vendor_id ? this.props.vendor_id : '',
				announcement_id: this.props.announcement_id
					? this.props.announcement_id
					: '',
				knowladgebase_id: this.props.knowladgebase_id
					? this.props.knowladgebase_id
					: '',
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
	}

	onChange = (e, key, type = 'single', from_type = '', array_values = []) => {
		if (type === 'single') {
			if (from_type === 'select') {
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
											/>
											<label
												htmlFor={`mvx-toggle-switch-${o.key}`}
											></label>
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
