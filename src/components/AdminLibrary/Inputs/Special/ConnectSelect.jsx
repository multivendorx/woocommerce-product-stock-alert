import { useState, useEffect } from "react";
import BasicInput from "../BasicInput";

import SelectInput from "../SelectInput";

import { getApiLink, getApiResponse } from "../../../../services/apiService";

import { useSetting } from "../../../../contexts/SettingContext";

import "./ConnectSelect.scss";

const ConnectSelect = (props) => {
  const { key, optionKey, settingChanged } = props;

  // State varaible for list of options
  const { setting, updateSetting } = useSetting();
  const [sellectOption, setSelectOption] = useState(setting[optionKey] || []);
  const [loading, setLoading] = useState(false);
  const [showOption, setShowOption] = useState(false);


  const updateSelectOption = async () => {
    const options = await getApiResponse(getApiLink(props.apiLink));
    settingChanged.current = true;
    updateSetting(optionKey, options);
    setSelectOption(options);
    setLoading(false);
    setShowOption(true);
  };

  return (
    <div className="connect-main-wrapper">
      <BasicInput
        wrapperClass="setting-form-input"
        descClass="settings-metabox-description"
        type={ 'text' }
        value={setting[key]}
        proSetting={false}
        onChange={(e) => {
          if ( ! props.proSettingChanged()) {
            props.onChange(e, key);
          }
        }}
      />
  
      <div className="button-wrapper">
        <button
          onClick={(e) => {
            e.preventDefault();
            if ( ! props.proSettingChanged()) {
              updateSelectOption();
              setLoading(true);
            }
          }}
        >
          Fetch List
        </button>

        {
          loading && (
          <div class="loader">
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
            <div class="three-body__dot"></div>
          </div>
          )
        }
      </div>
      
      { (sellectOption.length || showOption ) &&
        <SelectInput
          onChange={(e) => {
            e = { target: { value: e.value } };
            if ( ! props.proSettingChanged()) {
              props.onChange(e, props.selectKey);
            }
          }}
          options={sellectOption}
          value={props.value}
        />
      }
      
    </div>

  );
};

export default ConnectSelect;
