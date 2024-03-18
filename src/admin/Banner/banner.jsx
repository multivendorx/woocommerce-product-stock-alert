import React, { useState } from 'react'
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';

export default function banner() {
    if(localStorage.getItem('banner') != 'false'){
        localStorage.setItem("banner", true);
    }
    const [ modal, setModal ] = useState(false);
    const [ banner, setBanner ] = useState(localStorage.getItem('banner') == 'true' ? true : false);

    const handleCloseBanner = () => {
		localStorage.setItem('banner',false)
        setBanner(false);
	}

	const handleClose = () => {
        setModal(false);
	}
	const handleOpen = () => {
        setModal(true);
	}
    var $ = jQuery;
		$(document).ready(function () {
			const $carouselItems = $('.carousel-item');
			const totalItems = $carouselItems.length;
			let currentIndex = 0;
			let interval;

			// Function to show the current slide and hide others
			function showSlide(index) {
				$carouselItems.removeClass('active');
				$carouselItems.eq(index).addClass('active');
			}

			// Function to go to the next slide
			function nextSlide() {
				currentIndex = (currentIndex + 1) % totalItems;
				showSlide(currentIndex);
			}

			// Function to go to the previous slide
			function prevSlide() {
				currentIndex = (currentIndex - 1 + totalItems) % totalItems;
				showSlide(currentIndex);
			}

			// Start the auto-slide interval
			function startAutoSlide() {
				interval = setInterval(nextSlide, 7000); // Change slide every 7 seconds
			}

			// Stop the auto-slide interval
			function stopAutoSlide() {
				clearInterval(interval);
			}

			// Initialize the carousel
			showSlide(currentIndex);
			startAutoSlide();

			// Handle next button click
			$('#nextBtn').click(function () {
				nextSlide();
				stopAutoSlide();
				startAutoSlide();
			});

			// Handle previous button click
			$('#prevBtn').click(function () {
				prevSlide();
				stopAutoSlide();
				startAutoSlide();
			});
		});
    return (
        <>
            {stockManagerAppLocalizer.pro_active ? 
                banner ?
                    <div>
                        <Dialog
                            className="woo-module-popup"
                            open={modal}
                            onClose={handleClose}
                            aria-labelledby="form-dialog-title"
                        >	
                            <span 
                                className="icon-cross stock-manager-popup-cross"
                                onClick={handleClose}
                            ></span>
                            <Popoup/>
                        </Dialog>
                        <div className="woo-carousel-container">
                            <div className="carousel-container">
                                <div class="icon-cross pro-slider-cross" onClick={handleCloseBanner}></div>
                                <div class="why-go-pro-tag" onClick={handleOpen}>Why Premium</div>
                                <ul className="carousel-list">
                                    <li className="carousel-item active">
                                        <div className="woo-pro-txt-items">
                                            <h3>Double Opt-In {' '}</h3>
                                            <p>Experience the power of Double Opt-In for our Stock Alert Form - Guaranteed precision in every notification!{' '}</p>
                                            <a
                                                href={stockManagerAppLocalizer.pro_url}
                                                className="woo-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="woo-pro-txt-items">
                                            <h3>Your Subscription Hub{' '}</h3>
                                            <p>Subscription Dashboard - Easily monitor and download lists of out-of-stock subscribers for seamless management.{' '}</p>
                                            <a
                                                href={stockManagerAppLocalizer.pro_url}
                                                className="woo-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="woo-pro-txt-items">
                                            <h3>Mailchimp Bridge{' '}</h3>
                                            <p>Seamlessly link WooCommerce out-of-stock subscriptions with Mailchimp for effective marketing.{' '}</p>
                                            <a
                                                href={stockManagerAppLocalizer.pro_url}
                                                className="woo-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="woo-pro-txt-items">
                                            <h3>Unsubscribe Notifications{' '}</h3>
                                            <p>User-Initiated Unsubscribe from In-Stock Notifications.{' '}</p>
                                            <a
                                                href={stockManagerAppLocalizer.pro_url}
                                                className="woo-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                    <li class="carousel-item">
                                        <div className="woo-pro-txt-items">
                                            <h3>Ban Spam Emails {' '}</h3>
                                            <p>Email and Domain Blacklist for Spam Prevention.{' '}</p>
                                            <a
                                                href={stockManagerAppLocalizer.pro_url}
                                                className="woo-btn btn-red"
                                            >
                                                View Pricing
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="carousel-controls">
                                <button id="prevBtn"><i className='icon-left-arrow'></i></button>
                                <button id="nextBtn"><i className='icon-right-arrow'></i></button>
                            </div>
                        </div>
                    </div>
                : ''
            : ''
            }
        </>
    );
}
