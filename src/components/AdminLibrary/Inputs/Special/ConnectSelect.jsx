import { useState, useEffect } from "react";

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


  const updateSelectOption = async () => {
    const options = await getApiResponse(getApiLink(props.apiLink));  // console.log(options);
    settingChanged.current = true;
    updateSetting(optionKey, options);
    setSelectOption(options);
    setLoading(false);
  };

  return (
    <div className="connect-main-wrapper">
      <SelectInput
        onChange={(e) => {
          e = { target: { value: e.value } };
          props.onChange(e);
        }}
        options={sellectOption}
        value={props.value}
      />

      <div className="button-wrapper">
        <button
          onClick={(e) => {
            e.preventDefault();
            updateSelectOption();
            setLoading(true);
          }}
        >
          Connect
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
    </div>
  );
};

export default ConnectSelect;
