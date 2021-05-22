<footer class="footer" id="contact">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-form">
                    <center><div id="server_side_error"></div></center>
                    <h4>Get in Touch</h4>
                    <p class="form-message"></p>
                    <form id="contact-form" method="post" novalidate="novalidate">
                        <input style="margin-bottom:.5rem;" type="checkbox" id="subscription_interested" name="subscription_interested"/>
                        <label for="subscription_interested">I am interested in subscription.</label> 
                        <input style="margin-bottom:.5rem;" type="text" class="name" name="name" placeholder="Enter Your Name" />
                        <div class="row">
                            <div class="col-lg-6">
                                <input style="margin-bottom:.5rem;" type="text" class="phone_number" name="phone_number" placeholder="Mobile No" maxlength="10" />
                            </div>
                            <div class="col-lg-6">
                                <input style="margin-bottom:.5rem;" type="email" class="email" name="email" placeholder="Enter Your Email" />
                            </div>
                        </div>
                        <input style="margin-bottom:.5rem;" type="text" class="subject" name="subject" placeholder="Your Subject" />
                        <textarea style="margin-bottom:.5rem;" class="message" placeholder="Messege" name="message"></textarea>
                        <div class="row">
                            <div class="col-lg-6">
                                <input style="margin-bottom:.5rem;" type="text" class="phone_number" name="comment_captcha" placeholder="Captcha" maxlength="4" />
                            </div>
                            <div class="captcha-img col-lg-4"></div>
                            <div class="col-lg-2" style="margin-top: 6px;font-size: 23px;">
                                <a href="javascript:void(0);" title="Refresh Captcha" onclick="contact_captcha();"><i class="fa fa-refresh"></i></a>
                            </div>
                        </div>
                        <br/>
                        <button type="submit" name="submit" id="submitdata">Send Message</button>
                    </form>
                    <center><div id="result"></div></center>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-address">
                    <h4>Address</h4>
                    <p style="margin-bottom: 153px;">Godrej Woodsman Estate, Tower 4, B-401, Next to Kirloskar Business Park, Hebbal, Bengaluru KA 560024, India.</p>
                    <ul>
                        <li>
                            <div class="contact-address-icon">
                                <i class="icofont icofont-headphone-alt"></i>
                            </div>
                            <div class="contact-address-info">
								<a href="#">Comming soon</a>
                                <?php /* <a href="callto:#"></a> */ ?>
                            </div>
                        </li>
                        <li>
                            <div class="contact-address-icon">
                                <i class="icofont icofont-envelope"></i>
                            </div>
                            <div class="contact-address-info">
                                <a href="mailto:support@medsign.in">support@medsign.in</a>
                            </div>
                        </li>
                        <li>
                            <div class="contact-address-icon">
                                <i class="icofont icofont-web"></i>
                            </div>
                            <div class="contact-address-info">
                                <a href="//www.medsign.in">www.medsign.in</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-lg-12">
                <div class="subscribe-form">
                    <form id="subscriber" method="post" novalidate="novalidate">
                        <center><div id="subscriber_err_msg" style="margin-bottom:0px;"></div></center>
                        <input type="email" name="sub_email" class="sub_email hide" placeholder="Your email address here" />
                        <button type="submit" name="submit" class="hide">Subscribe</button>
                        <center><div id="subscriber_suc_msg" style="margin-bottom:0px;"></div></center>
                    </form>
                </div>
            </div>
        </div> -->
        <div class="row">
            <div class="col-lg-12">
                <div class="copyright-area">
                    <ul>
                        <li><a href="#"><i class="icofont icofont-social-facebook"></i></a></li>
                        <li><a href="#"><i class="icofont icofont-social-twitter"></i></a></li>
                        <li><a href="#"><i class="icofont icofont-brand-linkedin"></i></a></li>
                        <li><a href="#"><i class="icofont icofont-social-pinterest"></i></a></li>
                        <li><a href="#"><i class="icofont icofont-social-google-plus"></i></a></li>
                    </ul>
					<p>Synaegis Healthtech Pvt Ltd &copy; <script type="text/javascript">document.write(new Date().getFullYear());</script>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</footer>
<a href="#" class="scrollToTop"><i class="icofont icofont-arrow-up"></i></a>