import { useState, useEffect, useRef } from "react";
import "./dynamicForm.scss";
import CustomInput from "../Inputs";

// import context.
import { useSetting } from "../../../contexts/SettingContext";

// import services function
import { getApiLink, sendApiResponse } from "../../../services/apiService";
import Dialog from "@mui/material/Dialog";
import Popoup from "../../PopupContent/PopupContent";


const DynamicForm = (props) => {
  const { modal, submitUrl, id } = props.setting;
  const { setting, updateSetting } = useSetting();
  const [successMsg, setSuccessMsg] = useState("");
  const [countryState, setCountryState] = useState([]);
  const settingChanged = useRef(false);
  const [modelOpen, setModelOpen] = useState(false);

  // Submit the setting to backend when setting Change.
  useEffect(() => {
    if (settingChanged.current) {
      settingChanged.current = false;

      sendApiResponse(getApiLink(submitUrl), {
        setting: setting,
        settingName: id,
        vendor_id: props.vendorId || "",
        announcement_id: props.announcementId || "",
        knowladgebase_id: props.knowladgebaseId || "",
      }).then((response) => {
        // Set success messaage for 2second.
        setSuccessMsg(response.error);
        setTimeout(() => setSuccessMsg(""), 2000);

        // If response has redirect link then redirect.
        if (response.redirect_link) {
          window.location.href = response.data.redirect_link;
        }
      });
    }
  }, [setting]);

  const isProSetting = ( key ) => {
    return  appLocalizer.pro_active && props.proSetting?.includes( key );
  }


  const handleChange = (event, key, type = 'single', fromType = 'simple', arrayValue = []) => {
    if ( isProSetting( key ) ) {
        setModelOpen(true);
        return;
    }

    settingChanged.current = true;

    if ( type === 'single' ) {
        if (fromType === 'simple') {
            updateSetting( key, event.target.value );
        } else if (fromType === 'calender') {
            updateSetting( key, event.join( ',' ) );
        } else if (fromType === 'select') {
            updateSetting( key, arrayValue[ event.index ] );
        } else if (fromType === 'multi-select') {
            updateSetting( key, event );
        } else if (fromType === 'wpeditor') {
            updateSetting( key, event );
        } else if (fromType === 'country') {
            updateSetting( key, arrayValue[ event.index ] );
            const statefromcountrycode = JSON.parse(
                appLocalizer.countries.replace(/&quot;/g, '"')
            )[event.value];
            const country_list_array = [];
            for (const key_country in statefromcountrycode) {
                country_list_array.push({
                    label: key_country,
                    value: statefromcountrycode[key_country],
                });
            }
            setCountryState( country_list_array );
        }
    } else {
        let prevData = setting[key] || [];
        prevData = prevData.filter((data) => data != event.target.value);
        if ( event.target.checked ) {
            prevData.push( event.target.value );
        }
        updateSetting( key, prevData );
    }
}

  const handleMultiNumberChange = (e, key, optionKey, index) => {
    settingChanged.current = true;
    const mulipleOptions = setting[key] || {};
    mulipleOptions[index] = {
      key: optionKey,
      value: e.target.value,
    };
    updateSetting(key, mulipleOptions);
  };

  const handlMultiSelectDeselectChange = (e, m) => {
    settingChanged.current = true;
    if (setting[m.key].length > 0) {
      updateSetting(m.key, []);
    } else {
      const complete_option_value = [];
      {
        m.options
          ? m.options.map((o, index) => {
              complete_option_value[index] = o;
            })
          : "";
      }

      updateSetting(m.key, complete_option_value);
    }
  };

  const runUploader = (key) => {
    settingChanged.current = true;
    // Create a new media frame
    const frame = wp.media({
      title: "Select or Upload Media Of Your Chosen Persuasion",
      button: {
        text: "Use this media",
      },
      multiple: false, // Set to true to allow multiple files to be selected
    });

    frame.on("select", function () {
      // Get media attachment details from the frame state
      const attachment = frame.state().get("selection").first().toJSON();
      updateSetting(key, attachment.url);
    });
    // Finally, open the modal on click
    frame.open();
  };

  const renderForm = () => {
    return modal.map((inputField, index) => {
      let value = setting[inputField.key] || "";
      let input = "";

      // Check for dependent input fild
      if (inputField.depend && !setting[inputField.depend]) {
        return false;
      }

      // for select selection
      if (
        inputField.depend &&
        setting[inputField.depend] &&
        setting[inputField.depend].value &&
        setting[inputField.depend].value != inputField.dependvalue
      ) {
        return false;
      }

      // for radio button selection
      if (
        inputField.depend &&
        setting[inputField.depend] &&
        !setting[inputField.depend].value &&
        setting[inputField.depend] != inputField.dependvalue
      ) {
        return false;
      }

      // for checkbox selection
      if (
        inputField.depend_checkbox &&
        setting[inputField.depend_checkbox] &&
        setting[inputField.depend_checkbox].length === 0
      ) {
        return false;
      }

      // for checkbox selection
      if (
        inputField.not_depend_checkbox &&
        setting[inputField.not_depend_checkbox] &&
        setting[inputField.not_depend_checkbox].length > 0
      ) {
        return false;
      }

      // Set input fild based on their type.
      switch (inputField.type) {
        case "text":
        case "url":
        case "password":
        case "email":
        case "number":
          input = (
            <CustomInput.BasicInput
              wrapperClass="setting-form-input"
              descClass="settings-metabox-description"
              description={inputField.desc}
              key={inputField.key}
              id={inputField.id}
              name={inputField.name}
              type={inputField.type}
              placeholder={inputField.placeholder}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "textarea":
          input = (
            <CustomInput.TextArea
              wrapperClass="setting-from-textarea"
              inputClass={inputField.class || "form-input"}
              descClass="settings-metabox-description"
              description={inputField.desc}
              key={inputField.key}
              id={inputField.id}
              name={inputField.name}
              placeholder={inputField.placeholder}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "normalfile":
          input = (
            <CustomInput.BasicInput
              inputClass="setting-form-input"
              type="file"
              key={inputField.key}
              name={inputField.name}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "file":
          input = (
            <CustomInput.FileInput
              wrapperClass="setting-file-uploader-class"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={`${inputField.key} form-input`}
              imageSrc={value || appLocalizer.default_logo}
              imageWidth={inputField.width}
              imageHeight={inputField.height}
              buttonClass="btn btn-purple"
              openUploader={appLocalizer.global_string.open_uploader}
              type="hidden"
              key={inputField.key}
              name={inputField.name}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
              onButtonClick={(e) => {
                runUploader(inputField.key);
              }}
            />
          );
          break;

        case "color":
          input = (
            <CustomInput.BasicInput
              wrapperClass="settings-color-picker-parent-class"
              inputClass="setting-color-picker"
              descClass="settings-metabox-description"
              description={inputField.desc}
              key={inputField.key}
              id={inputField.id}
              name={inputField.name}
              type={inputField.type}
              value={value || "#000000"}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "calender":
          input = (
            <CustomInput.CalendarInput
              wrapperClass="settings-calender"
              inputClass="teal"
              multiple={true}
              value={setting[inputField.key]?.split(",") || ""}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key, "single", inputField.type);
              }}
            />
          );
          break;

        case "map":
          input = (
            <CustomInput.MapsInput
              wrapperClass="settings-basic-input-class"
              inputClass="regular-text"
              descClass="settings-metabox-description"
              description={inputField.desc}
              id="searchStoreAddress"
              placeholder="Enter store location"
              containerId="store-maps"
              containerClass="store-maps, gmap"
              proSetting={isProSetting(inputField.key)}
            />
          );
          break;

        case "button":
          input = (
            <div className="form-button-group">
              <div className="setting-section-divider">&nbsp;</div>
              <label className="settings-form-label"></label>
              <div className="settings-input-content">
                <CustomInput.BasicInput
                  wrapperClass="settings-basic-input-class"
                  inputClass="btn default-btn"
                  descClass="settings-metabox-description"
                  description={inputField.desc}
                  type={inputField.type}
                  placeholder={inputField.placeholder}
                  proSetting={isProSetting(inputField.key)}
                  // onChange={handleChange}
                />
              </div>
            </div>
          );
          break;

        case "multi_number":
          input = (
            <CustomInput.MultiNumInput
              parentWrapperClass="settings-basic-input-class"
              childWrapperClass="settings-basic-child-wrap"
              inputWrapperClass="settings-basic-input-child-class"
              innerInputWrapperClass="setting-form-input"
              inputLabelClass="setting-form-input-label"
              idPrefix="setting-integer-input"
              keyName={inputField.key}
              inputClass={inputField.class}
              value={setting[inputField.key]}
              options={inputField.options}
              onChange={handleMultiNumberChange}
              proSetting={isProSetting(inputField.key)}
            />
          );
          break;

        case "radio":
          input = (
            <CustomInput.RadioInput
              wrapperClass="settings-form-group-radio"
              inputWrapperClass="radio-input-label-wrap"
              inputClass="setting-form-input"
              descClass="settings-form-group-radio"
              activeClass="radio-select-active"
              description={inputField.desc}
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "radio_select":
          input = (
            <CustomInput.RadioInput
              wrapperClass="form-group-radio-select"
              inputWrapperClass="radioselect-class"
              inputClass="setting-form-input"
              radiSelectLabelClass="radio-select-under-label-class"
              labelImgClass="section-img-fluid"
              labelOverlayClass="radioselect-overlay-text"
              labelOverlayText="Select your Store"
              idPrefix="radio-select-under"
              descClass="settings-metabox-description"
              activeClass="radio-select-active"
              description={inputField.desc}
              type="radio-select"
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "radio_color":
          input = (
            <CustomInput.RadioInput
              wrapperClass="form-group-radio-color"
              inputWrapperClass="settings-radio-color "
              inputClass="setting-form-input"
              idPrefix="radio-color-under"
              activeClass="radio-color-active"
              descClass="settings-metabox-description"
              description={inputField.desc}
              type="radio-color"
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "toggle_rectangle":
          input = (
            <CustomInput.ToggleRectangle
              wrapperClass="settings-form-group-radio"
              inputWrapperClass="toggle-rectangle-merge"
              inputClass="setting-form-input"
              descClass="settings-metabox-description"
              idPrefix="toggle-rectangle"
              description={inputField.desc}
              value={value}
              name={inputField.name}
              keyName={inputField.key}
              options={inputField.options}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key);
              }}
            />
          );
          break;

        case "select":
          let options = inputField.options;
          // Check if option present in applocalizer.
          if (typeof options === "string") {
            options = appLocalizer[options];
          }

          input = (
            <CustomInput.SelectInput
              wrapperClass="form-select-field-wrapper"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={inputField.key}
              options={options}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e, data) => {
                handleChange(e, inputField.key, "single", "select", data);
              }}
            />
          );
          break;

        case "multi-select":
          input = (
            <CustomInput.SelectInput
              wrapperClass="settings-from-multi-select"
              descClass="settings-metabox-description"
              selectDeselectClass="select-deselect-trigger"
              selectDeselect={inputField.select_deselect}
              selectDeselectValue={
                appLocalizer.global_string.select_deselect_all
              }
              description={inputField.desc}
              inputClass={inputField.key}
              options={inputField.options}
              type="multi-select"
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e, data) => {
                handleChange(e, inputField.key, "single", "multi-select", data);
              }}
              onMultiSelectDeselectChange={(e) =>
                handlMultiSelectDeselectChange(e, inputField)
              }
            />
          );
          break;

        case "country":
          input = (
            <CustomInput.SelectInput
              wrapperClass="country-choice-class"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={inputField.key}
              options={inputField.options}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e, data) => {
                handleChange(e, inputField.key, "single", "country", data);
              }}
            />
          );
          break;

        case "state":
          input = (
            <CustomInput.SelectInput
              wrapperClass="state-choice-class"
              descClass="settings-metabox-description"
              description={inputField.desc}
              inputClass={inputField.key}
              options={countryState}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e, data) => {
                handleChange(e, inputField.key, "single", "select", data);
              }}
            />
          );
          break;

        case "checkbox":
          input = (
            <CustomInput.MultiCheckBox
              wrapperClass="checkbox-list-side-by-side"
              descClass="settings-metabox-description"
              description={inputField.desc}
              selectDeselectClass="select-deselect-trigger"
              inputWrapperClass="toggle-checkbox-header"
              inputInnerWrapperClass="toggle-checkbox-content"
              inputClass={inputField.class}
              hintOuterClass="dashicons dashicons-info"
              hintInnerClass="hover-tooltip"
              idPrefix="toggle-switch"
              selectDeselect={inputField.select_deselect}
              selectDeselectValue="Select / Deselect All"
              rightContentClass="settings-metabox-description"
              rightContent={inputField.right_content}
              options={inputField.options}
              value={value}
              proSetting={isProSetting(inputField.key)}
              onChange={(e) => {
                handleChange(e, inputField.key, "multiple");
              }}
              onMultiSelectDeselectChange={(e) =>
                handlMultiSelectDeselectChange(e, inputField)
              }
            />
          );
          break;

        case "table":
          input = (
            <CustomInput.Table
              wrapperClass="settings-form-table"
              tableWrapperClass="settings-table-wrap"
              trWrapperClass="settings-tr-wrap"
              thWrapperClass="settings-th-wrap"
              tdWrapperClass="settings-td-wrap"
              descClass="settings-metabox-description"
              headOptions={inputField.label_options}
              bodyOptions={inputField.options}
            />
          );
          break;

        case "wpeditor":
          input = (
            <CustomInput.WpEditor
              apiKey={appLocalizer.mvx_tinymce_key}
              value={value}
              onEditorChange={(e) => {
                handleChange(e, inputField.key, "simple", "wpeditor");
              }}
            />
          );
          break;

        case "label":
          input = (
            <CustomInput.Label
              wrapperClass="form-group-only-label"
              descClass="settings-metabox-description"
              value={inputField.valuename}
              description={inputField.desc}
            />
          );
          break;

        case "section":
          input = (
            <CustomInput.Section wrapperClass="setting-section-divider" />
          );
          break;

        case "blocktext":
          input = (
            <CustomInput.BlockText
              wrapperClass="blocktext-class"
              blockTextClass="settings-metabox-description-code"
              value={inputField.blocktext}
            />
          );
          break;

        case "separator":
          input = <CustomInput.Seperator wrapperClass="mvx_regi_form_box" />;
          break;

        // Special input type project specific
        case "button_customizer":
          input = (
            <CustomInput.ButtonCustomizer
              buttonText={setting.button_text}
              proSetting={isProSetting(inputField.key)}
              onChange={(e, key) => handleChange(e, key)}
            />
          );
          break;

        case "connect_select":
          input = (
            <CustomInput.ConnectSelect
              value={value}
              key={inputField.key}
              optionKey={inputField.optionKey}
              onChange={(e) => handleChange(e, inputField.key)}
              settingChanged={settingChanged}
              apiLink={inputField.apiLink}
            />
          );
          break;
        }

      return inputField.type === "section" ||
        inputField.label === "no_label" ? (
        input
      ) : (
        <div key={"g" + inputField.key} className="form-group">
          <label
            className="settings-form-label"
            key={"l" + inputField.key}
            htmlFor={inputField.key}
          >
            <p>{inputField.label}</p>
          </label>
          <div className="settings-input-content">{input}</div>
        </div>
      );
    });
  };

  const handleModelClose = () => {
    setModelOpen(false);
  };

  return (
    <>
      <div className="dynamic-fields-wrapper">
        <Dialog
          className="woo-module-popup"
          open={modelOpen}
          onClose={handleModelClose}
          aria-labelledby="form-dialog-title"
        >
          <span
            className="admin-font font-cross"
            onClick={handleModelClose}
          ></span>
          <Popoup />
        </Dialog>
        {successMsg && (
          <div className="notic-display-title">
            <i className="admin-font font-icon-yes"></i>
            {successMsg}
          </div>
        )}
        <form
          className="dynamic-form"
          onSubmit={(e) => {
            handleSubmit(e);
          }}
        >
          {renderForm()}
        </form>
      </div>
    </>
  );
};

export default DynamicForm;
