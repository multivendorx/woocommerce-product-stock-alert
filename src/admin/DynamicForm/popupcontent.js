/* global stockalertappLocalizer */
import React, { Component } from 'react';
import DialogContent from "@mui/material/DialogContent";
import DialogContentText from "@mui/material/DialogContentText";

class Propopup extends Component {

    render() {
        return (
            <>
                <DialogContent>
                    <DialogContentText>
                        <div className="woo-module-dialog-content">
                            <div className="woo-image-overlay">
                                <div className="woo-overlay-content">
                                    <h1 className="banner-header">Unlock <span className="banner-pro-tag">Pro</span> </h1>
                                    <div className="woo-banner-content">
                                        <strong>Boost to Product Stock Manager & Notifier Pro to access premium features!</strong>
                                        <p>&nbsp;</p>
                                        <p>1. Double Opt-in.</p>
                                        <p>2. Ban Spam Mail.</p>
                                        <p>3. Export Subscribers.</p>
                                        <p>4. Subscription Dashboard.</p>
                                        <p>5. MailChimp Integration.</p>
                                        <p>6. Recaptcha Support.</p>
                                        <p>7. Subscription Details.</p>
                                    </div>
                                    <div className="woo-banner-offer">Limited-Time Offer</div>
                                    <div className="discount-tag">Cupon Code: <b>{stockalertappLocalizer.pro_coupon_code}</b></div>
                                    <p className="">{stockalertappLocalizer.pro_coupon_text}</p>
                                    <a className="woo-go-pro-btn" target="_blank" href={stockalertappLocalizer.pro_url}>Upgrade to Pro</a>
                                </div>
                            </div>
                        </div>
                    </DialogContentText>
                </DialogContent>
            </>
        );
    }
}
export default Propopup;