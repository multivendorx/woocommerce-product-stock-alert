import React, { useEffect, useRef, useState } from 'react';
import './FormCustomizer.scss';
import ButtonCustomizer from './ButtonCustomizer';

const FormCustomizer = (props) => {
    const [currentHoverOn, setCurrentHoverOn] = useState('');
    const buttonRef = useRef();

    useEffect(() => {
        document.body.addEventListener("click", (event) => {
            if (!buttonRef.current.contains(event.target)) {
                setCurrentHoverOn('');
            }
        })
    }, [])
    return (
        <>
            <div className='fromcustomizer-wrapper'>
                <div className='wrapper-content'>
                    <div className='label-section'>
                        <p ref={currentHoverOn === 'description' ? buttonRef : null} onClick={(e) => setCurrentHoverOn('description')} className={currentHoverOn === 'description' && 'active'}>dsgsfdsz</p>
                    </div>
                    <div className='form-section'>
                        <div ref={currentHoverOn === 'email_input' ? buttonRef : null} className='input-section'>
                            <input readOnly onClick={(e) => setCurrentHoverOn('email_input')} className={currentHoverOn === 'email_input' && 'active'} type="email" placeholder='Place your email id' />

                            {currentHoverOn === 'email_input' && (
                                <>
                                    <div className='input-editor'>
                                        <p>Email</p><span><i className='admin-font font-edit'></i></span>
                                    </div>
                                </>
                            )}

                            <div className='setting-wrapper'>
                                <div className='seeting-nav'>...</div>
                                <button onClick={(e) => {
                                    e.preventDefault()
                                }} className="wrapper-close"><i class="admin-font font-cross"></i></button>
                                <div className="setting-section-dev">
                                    <span class="label">Placeholder text</span>
                                    <div class="property-section">
                                        <input type="email" placeholder='Put your email id' />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className='button-section'>
                            <ButtonCustomizer
                                buttonText={props.buttonText}
                                proSetting={props.proSetting}
                                onChange={props.onChange}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}

export default FormCustomizer