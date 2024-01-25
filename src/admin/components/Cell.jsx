import React, {useEffect,useState } from 'react';
import axios from 'axios';

const Cell = ({str,id,name,value}) => {
    const [inputValue, setInputValue] = useState('');
    const [isReadOnly, setIsReadOnly] = useState(true);
    const [btnclick,buttonClick] =useState(false);
    const [editted,inputEditted] =useState(false);

    const handleInputChange = (e) => {
        inputEditted(true);
        setInputValue(e.target.value);
    };
    const handleMouseOut = (e) => {
        if(btnclick | editted){
            document.addEventListener('click', handleDocumentClick);
        }
    };
    const buttonOnClick = (e) =>{        
        // console.log(e.target.parentElement);
        buttonClick(true);
        setIsReadOnly(false);
    };
    const handleDocumentClick = (event) => {
        if(editted){
            const data ={
                id: id,
                str:str,
                name: name,
                value: inputValue
            }
            axios({
                method: 'post',
                url: `${stockManagerAppLocalizer.apiUrl}/woo-stockmanager/v1/update`,
                data: data
            })
        }
        buttonClick(false);
        inputEditted(false);
        setIsReadOnly(true);
        document.removeEventListener('click', handleDocumentClick);
    };
    const style={
        width:'100%',
        background:'none',
        border:'none',
        
    }

    return(
        <>
            <input
                type="text"
                style={style}
                value={inputValue?inputValue:value}
                readOnly={isReadOnly}
                onChange={handleInputChange}
                onMouseOut={handleMouseOut}
            />
            <svg onClick={buttonOnClick} class="feather feather-edit-2" fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
            </svg>
        </>
    );
}

export default Cell;