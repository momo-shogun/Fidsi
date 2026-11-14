 
         
<!---==============footer start =================-->
      <div class="footer_area" id="footer_contents">
         <div class="footer_widgets_wrap bg_dark_2">
            
            <div class="pd_top_80"></div>
            
            <div class="container">
               <div class="row">
                  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                     <div class="footer_widgets about_company light_color">
                        <div class="about_company_inner">
                           <div class="footer_logo">
                              <a href="/" target="_blank" rel="nofollow">
                                 <img src="/assets/images/logo/Fidsi-logo-2 (2).png" class="img-fluid" alt="logo" />
                              </a>
                              
                              <div class="pd_bottom_30"></div>
                              
                           </div>
                           <div class="content_box">
                              <p>
                                 FIDSI (Federation of Indian Direct Selling Industries) is formed with an objective to make people and all the business sectors aware about the direct selling industry and its importance as an integral part of the Indian industrial sector.
                              </p>
                              
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                     <div class="footer_widgets wid_tit style_one">
                        <div class="fo_wid_title">
                           <h2>Get In Touch</h2>
                        </div>
                     </div>
                     
                     <div class="pd_bottom_25"></div>
                     
                     <div class="footer_widgets get_in_touch_foo light_color">
                        <div class="get_intouch_inrfo">
                           <div class="foo_cont_inner">
                              <div class="top">
                                 <h6> Address</h6>
                                 <p>
                                    1003–1004, 10th Floor, Aggarwal Cyber Plaza, Netaji Subhash Place, Pitampura, New Delhi – 110034
                                 </p>
                              </div>
                              <div class="bottom">
                                 <h6> Contact</h6>
                                 <div class="con_content">
                                    <h5> Phone :</h5>
                                    <a href="tel:+919643315150"> +91 95607 14455</a>
                                 </div>
                                 <div class="con_content">
                                    <h5> Mail Us :</h5>
                                    <a href="tel:support@fidsi.co.in"> support@fidsi.co.in</a>, <a href="tel:info@fidsi.co.in"> info@fidsi.co.in</a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-2 col-lg-4 col-md-4 col-sm-12">
                     <div class="footer_widgets wid_tit style_one">
                        <div class="fo_wid_title">
                           <h2>Company</h2>
                        </div>
                     </div>
                     
                     <div class="pd_bottom_25"></div>
                     
                     <div class="footer_widgets clearfix navigation_foo light_color style_one">
                        <div class="navigation_foo_box">
                           <div class="navigation_foo_inner">

                              <ul class="menu">
                                 <li><a href="/">Home</a></li>
                                 <li><a href="/about-us.php">About Us</a></li>
                                 <!--<li><a href="members.php">Our Members</a></li>-->
                                 <!--<li><a href="governingboard.php">Governing Board</a></li>-->
                                 <li><a href="/newsletter.php">NewsLetter</a></li>
                             
                                 <li><a href="/gallery.php">Gallery</a></li>
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-2 col-lg-4 col-md-4 col-sm-12">
                     <div class="footer_widgets wid_tit style_one">
                        <div class="fo_wid_title">
                           <h2>Information</h2>
                        </div>
                     </div>
                     
                     <div class="pd_bottom_25"></div>
                     
                     <div class="footer_widgets clearfix navigation_foo light_color style_one">
                        <div class="navigation_foo_box">
                           <div class="navigation_foo_box">
                              <div class="navigation_foo_inner">

                                 <ul class="menu">
                                   
                                      <!--<li><a href="govtletter.php">Govt Letter</a></li>-->
                                 <li><a href="/regulationsandcompliance.php">Acts and Rules</a></li>
                                 <li><a href="/news.php">News</a></li>
                                    <!--<li><a href="disclaimer.php">Disclaimer</a></li>-->
                                    <li><a href="/privacy-policy.php">Privacy Policy</a></li>
                                      
                                    
                                 </ul>

                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-xl-2 col-lg-4 col-md-4 col-sm-12">
                     <div class="footer_widgets wid_tit style_one">
                        <div class="fo_wid_title">
                           <h2>Useful Links</h2>
                        </div>
                     </div>
                     
                     <div class="pd_bottom_25"></div>
                     
                     <div class="footer_widgets clearfix navigation_foo light_color style_one">
                        <div class="navigation_foo_box">
                           <div class="navigation_foo_inner">

                              <ul class="menu">
                                 <li><a href="/joinfidsi.php">Join FIDSI</a></li>
                                 <li><a href="/contact-us.php">Contact Us</a></li>
                                 <li><a href="/blog">Blog</a></li>
                                 <!--<li><a href="#">Sitemap</a></li>-->
                              </ul>

                           </div>
                        </div>
                     </div>
                  </div>
               </div>

            <?php
            if (!isset($GLOBALS['mysqli']) || !($GLOBALS['mysqli'] instanceof mysqli)) {
                if (!defined('FIDSI_DB_OPTIONAL')) {
                    define('FIDSI_DB_OPTIONAL', true);
                }
                require_once __DIR__ . '/../setting.php';
            }
            require_once __DIR__ . '/../includes/blog.php';
            $blogDb = ($GLOBALS['mysqli'] ?? null) instanceof mysqli ? $GLOBALS['mysqli'] : null;
            $blogLatestPosts = $blogDb ? blog_latest_posts($blogDb, 5) : [];
            if ($blogLatestPosts):
            ?>
            <div class="footer-latest">
               <h2>Latest Posts</h2>
               <ul>
                  <?php foreach ($blogLatestPosts as $blogLatestPost): ?>
                  <li>
                     <a href="<?= blog_e(blog_post_path($blogLatestPost)) ?>"><?= blog_e($blogLatestPost['title']) ?></a>
                     <time datetime="<?= blog_e((string) $blogLatestPost['published_at']) ?>"><?= blog_e(blog_display_date((string) $blogLatestPost['published_at'])) ?></time>
                  </li>
                  <?php endforeach; ?>
               </ul>
            </div>
            <?php endif; ?>
            </div>
            
            <div class="pd_bottom_50"></div>
            
         </div>
         <div class="creote-footer-copyright bg_dark_3">

            <div class="container">
               <div class="row align-items-center">
                  <div class="col-lg-6 col-md-12 col-sm-12">
                     
                     <div class="pd_top_10"></div>
                     
                     <div class="footer_copy_content color_white">
                        © 2026 <a href="javascript:void(0)">FIDSI.</a> All Rights
                        Reserved.
                     </div>
                     
                     <div class="pd_bottom_10"></div>
                     
                  </div>
                  <div class="col-lg-6 col-md-12 col-sm-12 text-md-end">
                     
                     <div class="pd_top_10"></div>
                     
                     <div class="footer_copy_content_right">
                        <div class="social_media_v_one">
                           <ul>
                              <li>
                                 <a href="https://www.facebook.com/fidsiofficial" target="_blank">
                                    <span class="fa fa-facebook"></span>
                                    <small>facebook</small>
                                 </a>
                              </li>
                              <li>
                                 <a href="https://www.instagram.com/fidsi_/" target="_blank">
                                    <span class="fa fa-instagram"></span>
                                    <small>instagram</small>
                                 </a>
                              </li>
                              <li>
                                 <a href="https://www.linkedin.com/company/federations-of-indian-direct-selling-industries" target="_blank">
                                    <span class="fa fa-linkedin" target="_blank"></span>
                                    <small>LinkedIn</small>
                                 </a>
                              </li>
                              <li>
                                 <a href="https://www.youtube.com/@FIDSI-official" target="_blank">
                                    <span class="fa fa-youtube" target="_blank"></span>
                                    <small>Youtube</small>
                                 </a>
                              </li>
                            
                              
                           </ul>
                        </div>
                     </div>
                     
                     <div class="pd_bottom_10"></div>
                     
                  </div>
               </div>
            </div>

         </div>
      </div>
      <!---==============footer end =================-->
      <!---==============mobile menu =================-->
      <div class="crt_mobile_menu">
         <div class="menu-backdrop"></div>
         <nav class="menu-box">
            <div class="close-btn"><i class="icon-close"></i></div>
            <!--<form role="search" method="get" action="#">-->
            <!--   <input type="search" class="search" placeholder="Search..." value="" name="s" title="Search" />-->
            <!--   <button type="submit" class="sch_btn"> <i class="icon-search"></i></button>-->
            <!--</form>-->
            <div class="menu-outer">
               <!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header-->
            </div>
         </nav>
      </div>
      <!---==============mobile menu end =================-->
      <!---==============search popup =================-->
      <!--<div id="search-popup" class="search-popup">-->
      <!--   <div class="close-search"><i class="fa fa-times"></i></div>-->
      <!--   <div class="popup-inner">-->
      <!--      <div class="overlay-layer"></div>-->
      <!--      <div class="search-form">-->
      <!--         <fieldset>-->
      <!--            <form role="search" method="get" action="#">-->
      <!--               <input type="search" class="search" placeholder="Search..." value="" name="s" title="Search" />-->
      <!--               <button type="submit" class="sch_btn"> <i class="icon-search"></i></button>-->
      <!--            </form>-->
      <!--         </fieldset>-->
      <!--      </div>-->
      <!--   </div>-->
      <!--</div>-->
      <!---==============search popup end =================-->
      <!---==============modal popup =================-->
      <!--<div class="modal_popup one">-->
      <!--   <div class="modal-popup-inner">-->
      <!--      <div class="close-modal"><i class="fa fa-times"></i></div>-->
      <!--      <div class="modal_box">-->
      <!--         <div class="row">-->
      <!--            <div class="col-lg-5 col-md-12 form_inner">-->
      <!--               <div class="form_content">-->
      <!--                  <form class="contact-form" method="post" action="sendemail.php">-->
      <!--                     <p>-->
      <!--                        <label> Your name<br />-->
      <!--                           <input type="text" name="name" value="" size="40" aria-required="true"-->
      <!--                              aria-invalid="false" placeholder="Enter Your Name" />-->
      <!--                           <br />-->
      <!--                           <i class="fa fa-user"></i><br />-->
      <!--                        </label>-->
      <!--                     </p>-->
      <!--                     <p><label> Your email<br />-->
      <!--                           <input type="email" name="email" value="" size="40" aria-required="true"-->
      <!--                              aria-invalid="false" placeholder="Enter Your Email" />-->
      <!--                           <br />-->
      <!--                           <i class="fa fa-envelope"></i><br />-->
      <!--                        </label>-->
      <!--                     </p>-->
      <!--                     <p>-->
      <!--                        <label> Subject<br />-->
      <!--                           <input type="text" name="subject" value="" size="40" aria-required="true"-->
      <!--                              aria-invalid="false" placeholder="Enter Your Subject" />-->
      <!--                           <br />-->
      <!--                           <i class="fa fa-folder"></i><br />-->
      <!--                        </label>-->
      <!--                     </p>-->
      <!--                     <p>-->
      <!--                        <label> Your message (optional)<br />-->
      <!--                           <textarea name="message" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea"-->
      <!--                              aria-invalid="false" placeholder="Enter Your Message"></textarea>-->
      <!--                           <br />-->
      <!--                           <i class="fa fa-comments"></i><br />-->
      <!--                        </label>-->
      <!--                     </p>-->
      <!--                     <p><input type="submit" value="Submit" /></p>-->
      <!--                  </form>-->
      <!--               </div>-->
      <!--            </div>-->
      <!--            <div class="col-lg-7 col-md-12 about_company_inner">-->
      <!--               <div class="abt_content">-->
      <!--                  <div class="logo">-->
      <!--                     <img src="/assets/images/logo-default.png" alt="img" class="company_logo_modal">-->
      <!--                  </div>-->
      <!--                  <div class="text">-->
      <!--                     <p> The great explorer of the truth, the master-builder of human happiness no one rejects-->
      <!--                        dislikes avoids pleasure itself because it is pleasure but because know who do not those-->
      <!--                        how to pursue pleasures rationally encounter consequences that are extremely painful-->
      <!--                        desires to obtain.</p>-->
      <!--                     <a href="#">Read More</a>-->
      <!--                  </div>-->
      <!--                  <div class="post_contet_modal">-->
      <!--                     <h2> Latest News</h2>-->
      <!--                     <div class="post_enable">-->
      <!--                        <div class="modal_post_grid">-->
      <!--                           <a href="blog-single.html">-->
      <!--                              <img width="852" height="812" src="/assets/images/blog/blog-image-9.jpg"-->
      <!--                                 class="main_img wp-post-image" alt="img" />-->
      <!--                           </a>-->
      <!--                        </div>-->
      <!--                        <div class="modal_post_grid">-->
      <!--                           <a href="blog-single.html">-->
      <!--                              <img width="852" height="812" src="/assets/images/blog/blog-image-8.jpg"-->
      <!--                                 class="main_img wp-post-image" alt="img" />-->
      <!--                           </a>-->
      <!--                        </div>-->
      <!--                        <div class="modal_post_grid">-->
      <!--                           <a href="blog-single.html">-->
      <!--                              <img width="852" height="812" src="/assets/images/blog/blog-image-7.jpg"-->
      <!--                                 class="main_img wp-post-image" alt="img" />-->
      <!--                           </a>-->
      <!--                        </div>-->
      <!--                        <div class="modal_post_grid">-->
      <!--                           <a href="blog-single.html">-->
      <!--                              <img width="852" height="812" src="/assets/images/blog/blog-image-6.jpg"-->
      <!--                                 class="main_img wp-post-image" alt="img" />-->
      <!--                           </a>-->
      <!--                        </div>-->
      <!--                        <div class="modal_post_grid">-->
      <!--                           <a href="blog-single.html">-->
      <!--                              <img width="852" height="812" src="/assets/images/blog/blog-image-5.jpg"-->
      <!--                                 class="main_img wp-post-image" alt="img" />-->
      <!--                           </a>-->
      <!--                        </div>-->
      <!--                     </div>-->
      <!--                  </div>-->
      <!--                  <div class="copright">-->
      <!--                     © 2023 Creote. All Rights Reserved.-->
      <!--                  </div>-->
      <!--               </div>-->
      <!--            </div>-->
      <!--         </div>-->
      <!--      </div>-->
      <!--   </div>-->
      <!--</div>-->
      <!---==============modal popup end=================-->
      <!---==============cart=================-->
      <div class="side_bar_cart" id="mini_cart">
         <div class="cart_overlay"></div>
         <div class="cart_right_conten">
            <div class="close">
               <div class="close_btn_mini"><i class="icon-close"></i></div>
            </div>
            <div class="cart_content_box">
               <div class="contnet_cart_box">
                  <div class="widget_shopping_cart_content">
                     <p class="woocommerce-mini-cart__empty-message">No products in the cart.</p>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!---==============cart=================-->
      <!---
      <div class="floating_menu_box">
         <ul class="float_menu_box">
            <i class="close fa fa-times"></i>
            <li class="floating_menu_text active">
               <a href="#home"> Home </a>
            </li>

            <li class="floating_menu_text ">
               <a href="#about">About </a>
            </li>

            <li class="floating_menu_text ">
               <a href="#service"> Service </a>
            </li>

            <li class="floating_menu_text ">
               <a href="#process"> Process </a>
            </li>

            <li class="floating_menu_text ">
               <a href="#projects"> Projects </a>
            </li>

            <li class="floating_menu_text">
               <a href="#blog"> Blog </a>
            </li>
         </ul>
      </div>
      -->