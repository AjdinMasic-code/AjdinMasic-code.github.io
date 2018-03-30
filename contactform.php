<?php
// OPTIONS - PLEASE CONFIGURE THESE BEFORE USE!

$yourEmail = "ajdinmasic@boisewebsolutions.com"; // the email address you wish to receive these mails through
$yourWebsite = "Web Solutions LLC"; // the name of your website
$thanksPage = ''; // URL to 'thanks for sending mail' page; leave empty to keep message on the same page 
$maxPoints = 4; // max points a person can hit before it refuses to submit - recommend 4
$requiredFields = "name,email,comments"; // names of the fields you'd like to be required as a minimum, separate each field with a comma


// DO NOT EDIT BELOW HERE
$error_msg = array();
$result = null;

$requiredFields = explode(",", $requiredFields);

function clean($data) {
	$data = trim(stripslashes(strip_tags($data)));
	return $data;
}
function isBot() {
	$bots = array("Indy", "Blaiz", "Java", "libwww-perl", "Python", "OutfoxBot", "User-Agent", "PycURL", "AlphaServer", "T8Abot", "Syntryx", "WinHttp", "WebBandit", "nicebot", "Teoma", "alexa", "froogle", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "WebFindBot", "girafabot", "crawler", "www.galaxy.com", "Googlebot", "Scooter", "Slurp", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz");

	foreach ($bots as $bot)
		if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false)
			return true;

	if (empty($_SERVER['HTTP_USER_AGENT']) || $_SERVER['HTTP_USER_AGENT'] == " ")
		return true;
	
	return false;
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (isBot() !== false)
		$error_msg[] = "No bots please! UA reported as: ".$_SERVER['HTTP_USER_AGENT'];
		
	// lets check a few things - not enough to trigger an error on their own, but worth assigning a spam score.. 
	// score quickly adds up therefore allowing genuine users with 'accidental' score through but cutting out real spam :)
	$points = (int)0;
	
	$badwords = array("adult", "beastial", "bestial", "blowjob", "clit", "cum", "cunilingus", "cunillingus", "cunnilingus", "cunt", "ejaculate", "fag", "felatio", "fellatio", "fuck", "fuk", "fuks", "gangbang", "gangbanged", "gangbangs", "hotsex", "hardcode", "jism", "jiz", "orgasim", "orgasims", "orgasm", "orgasms", "phonesex", "phuk", "phuq", "pussies", "pussy", "spunk", "xxx", "viagra", "phentermine", "tramadol", "adipex", "advai", "alprazolam", "ambien", "ambian", "amoxicillin", "antivert", "blackjack", "backgammon", "texas holdem", "poker", "carisoprodol", "ciara", "ciprofloxacin", "debt", "dating", "porn", "link=", "voyeur", "content-type", "bcc:", "cc:", "document.cookie", "onclick", "onload", "javascript");

	foreach ($badwords as $word)
		if (
			strpos(strtolower($_POST['comments']), $word) !== false || 
			strpos(strtolower($_POST['name']), $word) !== false
		)
			$points += 2;
	
	if (strpos($_POST['comments'], "http://") !== false || strpos($_POST['comments'], "www.") !== false)
		$points += 2;
	if (isset($_POST['nojs']))
		$points += 1;
	if (preg_match("/(<.*>)/i", $_POST['comments']))
		$points += 2;
	if (strlen($_POST['name']) < 3)
		$points += 1;
	if (strlen($_POST['comments']) < 15 || strlen($_POST['comments']) > 1500)
		$points += 2;
	if (preg_match("/[bcdfghjklmnpqrstvwxyz]{7,}/i", $_POST['comments']))
		$points += 1;
	// end score assignments

	if ( !empty( $requiredFields ) ) {
		foreach($requiredFields as $field) {
			trim($_POST[$field]);
			
			if (!isset($_POST[$field]) || empty($_POST[$field]) && array_pop($error_msg) != "Please fill in all the required fields and submit again.\r\n")
				$error_msg[] = "Please fill in all the required fields and submit again.";
		}
	}

	if (!empty($_POST['name']) && !preg_match("/^[a-zA-Z-'\s]*$/", stripslashes($_POST['name'])))
		$error_msg[] = "The name field must not contain special characters.\r\n";
	if (!empty($_POST['email']) && !preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', strtolower($_POST['email'])))
		$error_msg[] = "That is not a valid e-mail address.\r\n";
	if (!empty($_POST['url']) && !preg_match('/^(http|https):\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)(:(\d+))?\/?/i', $_POST['url']))
		$error_msg[] = "Invalid website url.\r\n";
	
	if ($error_msg == NULL && $points <= $maxPoints) {
		$subject = "Automatic Form Email";
		
		$message = "You received this e-mail message through your website: \n\n";
		foreach ($_POST as $key => $val) {
			if (is_array($val)) {
				foreach ($val as $subval) {
					$message .= ucwords($key) . ": " . clean($subval) . "\r\n";
				}
			} else {
				$message .= ucwords($key) . ": " . clean($val) . "\r\n";
			}
		}
		$message .= "\r\n";
		$message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
		$message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
		$message .= 'Points: '.$points;

		if (strstr($_SERVER['SERVER_SOFTWARE'], "Win")) {
			$headers   = "From: $yourEmail\r\n";
		} else {
			$headers   = "From: $yourWebsite <$yourEmail>\r\n";	
		}
		$headers  .= "Reply-To: {$_POST['email']}\r\n";

		if (mail($yourEmail,$subject,$message,$headers)) {
			if (!empty($thanksPage)) {
				header("Location: $thanksPage");
				exit;
			} else {
				$result = 'Your mail was successfully sent.';
				$disable = true;
			}
		} else {
			$error_msg[] = 'Your mail could not be sent this time. ['.$points.']';
		}
	} else {
		if (empty($error_msg))
			$error_msg[] = 'Your mail looks too much like spam, and could not be sent this time. ['.$points.']';
	}
}
function get_data($var) {
	if (isset($_POST[$var]))
		echo htmlspecialchars($_POST[$var]);
}
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <!--  The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags  -->
        <meta name="description" content="Contact the team! We offer Boise web design services to all of Idaho. Not just Boise. We offer exceptional web design, web development, and customer service." />
        <meta name="theme-color" content="#ff6666" />
        <!--  Makes the address bar change color for android devices with v 5.0 lollipop w/ chrome browsers  -->
        <meta name="author" content="Web Solutions LLC, 208-982-2181" />
        <link rel="icon" href="favicon.ico" />
        <link rel="canonical" href="https://boisewebsolutions.com/contactform.php">

        <title>Contact the Boise Web Design Team | Web Solutions LLC</title>

        <!--  font style and font awesome -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:500,300" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="fonts/font-awesome/css/font-awesome.min.css" />

        <!--  Bootstrap core CSS  -->
        <link href="css/bootstrap.min.css" rel="stylesheet" />


        <!--  Custom styles for this template  -->
        <link href="css/custom.css" rel="stylesheet" />

        <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                    m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-78740818-3', 'auto');
            ga('send', 'pageview');

        </script>


    </head>


    <body class="not-front" id="contact" data-spy="scroll" data-target=".navbar-collapse" data-offset="150">

        <style type="text/css">
            .jumbotron {
                background: url("images/contact_img.jpg");
                filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/contact_img.jpg', sizingMethod='scale');
                -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='images/contact_img.jpg', sizingMethod='scale')";
                background-position: 35% 50%;
                height: 350px;
            }

            /* Validation styling */

            .confirmation-spacing {
                padding-top: 100px;
                margin-top: -100px;
                padding-bottom: 15px;
            }

            p.error,
            p.success {
                font-weight: bold;
                padding: 10px;
                border: 1px solid;
            }

            p.error {
                background: #ffc0c0;
                color: #900;
            }

            p.success {
                background: #b3ff69;
                color: #4fa000;
            }

            .email-phone {
                margin-top: 20px;
                padding-left: 0;
            }

            .email-phone p {
                text-align: left;
                font-size: 1.4em;
            }

            .contact-info {
                width: 50%;
                float: left;
                padding-right: 50px;
                line-height: 1.6;
            }

            .contact-info .first-in-list {
                padding-top: 2.5em;
            }

            .contact-info ul li {
                padding-bottom: 2.5em;
            }

            .contactUs {
                margin-top: 25px;
            }

            /* Form Styling  Start*/

            input,
            label,
            textarea {
                display: block;
            }

            form {
                float: left;
                display: inline;
                width: 50%;
            }

            input,
            textarea,
            select {
                width: 100%;
                opacity: .5;
                border: 2px solid black;
                transition: opacity .38s;
                /* smooth and quick transitioning */
                -o-transition: opacity .38s;
                -webkit-transition: opacity .38s;
                -moz-transition: opacity .38s;
                -ms-transition: opacity .38s;
            }

            input,
            select {
                height: 3.5rem;
            }

            textarea {
                height: 20rem;
                resize: none;
            }

            input:focus,
            textarea:focus,
            select:focus {
                opacity: 1;
                outline: none!important;
                border: 2px solid black;
            }

            input[type="checkbox"] {
                width: 20px;
                height: 20px;
                padding: 0;
                margin: 0;
                vertical-align: bottom;
                position: relative;
                top: 9px;
                display: inline;
                margin-bottom: 10px;
                opacity: 1!important;
            }

            #commentsLabel,
            #servicesLabel {
                margin-top: 20px;
            }


            input[type="submit"] {
                opacity: 1;
                border: none;
            }

            .form-contact-info-container {
                width: 100%;
                position: relative;
                height: 100%;
                margin-top: 25px;
            }

            form {
                margin-bottom: 75px;
            }

            .container h2 {
                text-align: center;
                margin-top: 50px;
            }

            #map {
                height: 400px;
                width: 100%;
                margin-bottom: 50px;
            }

            @media (max-width: 929px) {
                .email-phone p {
                    font-size: 16px;
                }
            }

            @media (max-width: 767px) {
                .contact-info {
                    width: 100%;
                }

                form {
                    width: 100%;
                    margin-left: 0;
                }
            }

            @media (max-width: 550px) {
                .contact-info .first-in-list {
                    padding-top: 1.5em;
                }

                .contact-info ul li {
                    padding-bottom: 1.5em;
                }
            }

        </style>
        
        <!-- JSON-LD markup generated by Google Structured Data Markup Helper. -->
        <script type="application/ld+json">
            {
                "@context": "http://schema.org",
                "@type": "LocalBusiness",
                "name": "Web Solutions LLC",
                "image": "http://boisewebsolutions.com/images/logo.png",
                "telephone": "(208) 614-0423",
                "email": "ajdinmasic@boisewebsolutions.com",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "P.O. Box is 191153",
                    "addressLocality": "Boise",
                    "addressRegion": "ID",
                    "postalCode": "83719-1153"
                },
                "openingHoursSpecification": {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": {
                        "@type": "DayOfWeek",
                        "name": "Monday, Tuesday, Wednesday, Thursday, Friday"
                    },
                    "opens": "10:00",
                    "closes": "17:00"
                }
            }

        </script>


        <span itemscope itemtype="http://schema.org/LocalBusiness"><nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
        <img itemprop="image" src="images/logo.png" id="logo-indent" /><a class="navbar-brand" href="index.html" id="title">
<span itemprop="name">Web Solutions</span></a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="http://www.boisewebsolutions.com">Home</a></li>
                <li><a href="http://www.boisewebsolutions.com#who-we-are">About</a></li>
                <li><a href="http://www.boisewebsolutions.com#services">Services</a></li>
                <li class="active"><a href="contactform.php">Contact</a></li>
                <li><a href="portfolio.html">Portfolio</a></li>
                <li><a href="the-importance-of-having-a-website-in-2017.html">Blog</a></li>
            </ul>
        </div>
        <!-- /.nav-collapse  -->
        </nav>

        <!--  Main jumbotron for a primary marketing message or call to action  -->
        <div id="home">
            <div class="jumbotron">
                <div class="container">
                </div>
            </div>
        </div>

        <div class="container contactUs">
            <h2>Get In Touch With Us</h2>
            <hr>
        </div>
        <!--
	Copyright Web Solutions LLC 2017
-->

        <div class="container">
            <div class="form-contact-info-container">


                <?php
                    if (!empty($error_msg)) {
                        echo '<div id="formAnchor" class="confirmation-spacing"><p class="error">ERROR: '. implode("<br />", $error_msg) . "</p></div>";
                    }
                    if ($result != NULL) {
                        echo '<div id="formAnchor" class="confirmation-spacing"><p class="success">'. $result . "</p></div>";
                    }
                ?>


                    <div class="contact-info">
                        <p>
                            Web Solutions, LLC is the leading provider of web development and design, trusted by many Idaho businesses and organizations. Find out how Web Solutions, LLC can drive internet marketing and actionable insights, so you can deliver results.
                        </p>

                        <p>
                            <strong>Ask a Web Solutions expert how we can help:</strong>
                        </p>

                        <ul>
                            <li class="first-in-list">Why websites are <strong>mandatory</strong> in todays market</li>
                            <li>How Web Solutions LLC can <strong>improve sales</strong>, <strong>advertising</strong>, and more...</li>
                            <li>What our web design and web development team can provide for you</li>
                            <li>Information about <strong>pricing</strong> and more!</li>
                            <li>Ask us about our deals!</li>
                        </ul>

                        <div class="email-phone">

                            <p>Phone: <a href="tel:+12089822181" class="mobilephone">(208) 614-0423</a><span itemprop="telephone" class="desktopphone">(208) 614-0423</span></p>

                            <p>Email:<a href="mailto:ajdinmasic@boisewebsolutions.com">
                        <span itemprop="email">ajdinmasic@boisewebsolutions.com</span></a>
                            </p>
                            <p><strong>We provide web design and development services to all of Idaho</strong></p>

                        </div>

                    </div>



                    <form action="contactform.php#formAnchor" method="post">
                        <noscript>
                        <p><input type="hidden" name="nojs" id="nojs" /></p>
                </noscript>
                        <p>
                            <label for="name">Name: *</label>
                            <input type="text" name="name" id="name" value="<?php get_data(" name "); ?>" /><br />

                            <label for="email">E-mail: *</label>
                            <input type="text" name="email" id="email" value="<?php get_data(" email "); ?>" /><br />

                            <label for="url">Website URL:</label>
                            <input type="text" name="url" id="url" value="<?php get_data(" url "); ?>" /><br />

                            <label for="location">Location:</label>
                            <input type="text" name="location" id="location" value="<?php get_data(" location "); ?>" /><br />

                            <label for="budget">Budget:</label>
                            <select name="budget" id="budget">
                              <option value="Under $500">Under $500</option>
                              <option value="$500-$1000">$500-$1000</option>
                              <option value="$1000-$3000">$1000-$3000</option>
                              <option value="$3000-$5000">$3000-$5000</option>
                              <option value="$5000-$10000">$5000-$10000</option>
                              <option value="$10000+">$10000+</option>
                        </select><br />

                            <label id="servicesLabel">Which Services Are You Interested In?</label>
                            <input type="checkbox" name="servicesInterestedIn[]" value="New Website"> <span>New website</span><br>
                            <input type="checkbox" name="servicesInterestedIn[]" value="Update old website"> <span>Update old website</span><br>
                            <input type="checkbox" name="servicesInterestedIn[]" value="Mobile web development"> <span>Mobile Web Development</span><br>
                            <input type="checkbox" name="servicesInterestedIn[]" value="E-Commerce"> <span>E-Commerce</span><br>
                            <input type="checkbox" name="servicesInterestedIn[]" value="SEO"> <span>Search Engine Optimization</span><br>
                            <input type="checkbox" name="servicesInterestedIn[]" value="Weekly/Monthly Blogging"> <span>Weekly/Monthly Blogging</span><br>

                            <label for="comments" id="commentsLabel">Message: *</label>
                            <textarea name="comments" id="comments"><?php get_data("comments"); ?></textarea><br />
                        </p>
                        <p>
                            <input type="submit" name="submit" id="submit" value="Send" <?php if (isset($disable) && $disable===true) echo ' disabled="disabled"'; ?> />
                        </p>
                    </form>
            </div>

        </div>

        <!---=================== Google Map ======================-->
        <div class="container">
            <div id="map"></div>
        </div>

        <script>
            function initMap() {
                // Styles a map in night mode.
                var map = new google.maps.Map(document.getElementById('map'), {
                    center: {
                        lat: 43.6187,
                        lng: -116.2146
                    },
                    zoom: 12,
                    styles: [{
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#212121"
                            }]
                        },
                        {
                            "elementType": "labels.icon",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#757575"
                            }]
                        },
                        {
                            "elementType": "labels.text.stroke",
                            "stylers": [{
                                "color": "#212121"
                            }]
                        },
                        {
                            "featureType": "administrative",
                            "elementType": "geometry",
                            "stylers": [{
                                    "color": "#757575"
                                },
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative.country",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#9e9e9e"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "administrative.locality",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#bdbdbd"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "labels.text",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#757575"
                            }]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#181818"
                            }]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#616161"
                            }]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "labels.text.stroke",
                            "stylers": [{
                                "color": "#1b1b1b"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry.fill",
                            "stylers": [{
                                "color": "#ffffff"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.icon",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#8a8a8a"
                            }]
                        },
                        {
                            "featureType": "road.arterial",
                            "elementType": "geometry",
                            "stylers": [{
                                    "color": "#cccccc"
                                },
                                {
                                    "weight": 0.5
                                }
                            ]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#e63c3c"
                            }]
                        },
                        {
                            "featureType": "road.highway.controlled_access",
                            "elementType": "geometry",
                            "stylers": [{
                                    "color": "#e63c3c"
                                },
                                {
                                    "weight": 0.5
                                }
                            ]
                        },
                        {
                            "featureType": "road.local",
                            "elementType": "labels",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "road.local",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#616161"
                            }]
                        },
                        {
                            "featureType": "transit",
                            "stylers": [{
                                "visibility": "off"
                            }]
                        },
                        {
                            "featureType": "transit",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#757575"
                            }]
                        },
                        {
                            "featureType": "water",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#000000"
                            }]
                        },
                        {
                            "featureType": "water",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#3d3d3d"
                            }]
                        }
                    ]
                });
            }

        </script>

        </span>

        <footer>
        <div class="container">
            <h3>© 2017 Web Solutions LLC</h3>
            <p  class='footer-tag'><strong>Serving Boise, Idaho and the entire treasure valley</strong></p>
            <a target="_blank" href="https://www.facebook.com/WebSolutionsLLC/"><i class="fa fa-facebook-official fa-3" aria-hidden="true"></i></a>
        </div>
            <div class="footer-img">
                <img src="images/mountains-opt3.png" class="mountain">
            </div>
            <div class="footer-contact">
                <div class="container">
                    <p class="footer-phone">Phone: (208) 614-0423</p>
                    <p class="footer-email">Email: ajdinmasic@boisewebsolutions.com</p>
                </div>
            </div>
        </footer>


        <div id="back-to-top" style="display:none;">
            <a href="#home" id="arrow-transition"><i class="fa fa-3x fa-arrow-circle-up" aria-hidden="true" id="arrow"></i></a>
        </div>


        <!--  Bootstrap core JavaScript
    ==================================================  -->
        <!--  Placed at the end of the document so the pages load faster  -->
        <!--  Jquery  -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDSiLysnKGUWCnFEqnu54rSq2sf0N2g3uc&callback=initMap" async defer></script>

        <!--  Makes sure the navbar collapses  -->
        <script src="js/bootstrap.min.js"></script>

        <!--  Main JavaScript  -->
        <script src="js/main.js"></script>


    </body>

    </html>
