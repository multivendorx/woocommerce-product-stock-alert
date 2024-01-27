/* global stockManagerAppLocalizer */
import React, { Component } from 'react';
import Dialog from "@mui/material/Dialog";
import Popoup from '../PopupContent/PopupContent';
import axios from 'axios';
class Banner extends Component {
	constructor(props) {
		super(props);
		this.state = {
			open_model: false,
			open_banner : false,
		};
		this.handleClose = this.handleClose.bind(this);
		this.handleOpen = this.handleOpen.bind(this);
		this.handleCloseBanner = this.handleCloseBanner.bind(this);
	}

	handleCloseBanner() {
		axios
			.get(
				`${stockManagerAppLocalizer.apiUrl}/woo-stockmanager/v1/close-banner`,
			)
			.then((response) => {
				this.setState({
					open_banner: response.data,
				});
			});
	}

	handleClose() {
		this.setState({
			open_model: false,
		});
	}
	handleOpen() {
		this.setState({
			open_model: true,
		});
	}
	componentDidMount() {

		this.setState({
			open_banner: stockManagerAppLocalizer.banner_show,
		});

		var $ = jQuery;
		$(document).ready(function () {
			const $carouselList = $('.carousel-list');
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
	}

	render() {
		return (
			<>
				{stockManagerAppLocalizer.pro_active ? 
					this.state.open_banner ?
						<div>
							<Dialog
								className="woo-module-popup"
								open={this.state.open_model}
								onClose={this.handleClose}
								aria-labelledby="form-dialog-title"
							>	
								<span 
									className="icon-cross stock-manager-popup-cross"
									onClick={this.handleClose}
								></span>
								<Popoup/>
							</Dialog>
							<div className="woo-carousel-container">
							<div className="carousel-container">
								<div class="icon-cross pro-slider-cross" onClick={this.handleCloseBanner}></div>
								<div class="why-go-pro-tag" onClick={this.handleOpen}>Why Premium</div>
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
					: ''}
			</>
		);
	}
}
export default Banner;