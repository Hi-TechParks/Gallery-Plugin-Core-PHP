<?php
header('Content-type: text/css');
?>




/* ADMIN HOME
 ----------------------------------------*/
 input[type=submit], input[type=text], textarea, input[type=cancel]{
 	-webkit-appearance: none;
 }
 body{
 	margin-bottom: 50px;
 }
.login_form_container{
	margin: 0 auto;
	margin-top: 30px;
	text-align: center;
}
.login_username_wrapper label, .login_password_wrapper label{
	display: inline-block;
	font-size: 1.3em;
	min-width: 105px;
	text-align: right;
	padding-right: 5px;
	margin-bottom: 15px;
	box-sizing: border-box;
}
.login_username_wrapper input, .login_password_wrapper input{
	display: inline-block;
	font-size: 1.3em;
	width: 175px;
	text-align: left;
	padding: 0 5px 0 5px;
	margin-bottom: 15px;
	box-sizing: border-box;
	background: #f7f7f7;
	border: 1px solid #ccc;
}
.login_button_wrapper{
	margin: 8px 0 0 0;
}
.login_button_wrapper input{
	font-size: 1.3em !important;
}
.login_password_recovery_wrapper{
	margin: 25px 0 0 0;
}

.admin_body{
<?php
	if($_GET['wp']=true){
		echo "background: transparent;";
	}
?>
}
.admin_container .admin_create_new_gallery{
	margin: 40px 0 20px 0;
}
.admin_container .gallery_listing_tile .admin_options{
	margin: 10px 0 10px 0;
}
.admin_container h1.admin_page_title{
	max-width: 100%;
	font-size: 1.5em;
	text-align: center;
	margin: 0 5px 5px 5px;
	padding: 0;
}
.admin_container .admin_help h1{
	font-size: 1.5em;
	margin: 0 0 5px 0;
}
.admin_container .admin_help ul{
	text-align: left;
	width: 80%;
	margin: 0 50px 0 50px;
}
.admin_container .admin_help ul li{
	padding: 0px;
	margin: 0px;
	margin-left: 20px;
	line-height: 1.3em;
}
.admin_container .admin_help ul li span{
	margin: 0 0 0 -8px;
}
.admin_container .wordpress_instructions h1{
	font-size: 1.5em;
	margin: 30px 0 5px 0;
}
.admin_container .wordpress_instructions p{
	margin: 0 50px 0 50px;
	text-align: left;
	line-height: 1.3em;
}
.admin_sort_form_container{
	margin: 20px 0 20px 0;
	text-align: right;
}
.admin_sort_form_container form label, .admin_sort_form_container .sort_form_button{
	display: none;
}
.admin_sort_form_container form select{
	margin-right: 8px;
}

.admin_form{
	width: 100%;
	box-sizing: border-box;
	margin: 0 auto;
}
.admin_form input[type="text"], .admin_form input[type="email"], .admin_form input[type="password"], .admin_form textarea, .admin_form select{
	font-size: 1.1em;
	padding: 3px 5px 3px 5px;
	width: calc(100% - 10px);
	min-width: 200px;
	max-width: 500px;
	margin: 0 5px 15px 5px;
	box-sizing: border-box;
}
.admin_form input[type="text"], .admin_form input[type="email"], .admin_form input[type="password"], .admin_form textarea, .admin_form select{
	background: #f7f7f7;
	border: 1px solid #ccc;
}
.admin_form .admin_form_row{
	text-align: center;
}
.admin_form .admin_form_row label{
	font-weight: bold;
	vertical-align: top;
}




/* ACTION PAGES HOME (DELETE GALLERY, EDIT GALLERY, DELETE IMAGE, SET THUMB, SET CAPTION) 
 ----------------------------------------*/
 .admin_gallery_details{
 	width: 100%;
 	max-width: 600px;
 	margin: 0 auto;
 	margin-top: 20px;
 	margin-bottom: 20px;
 }
 .upload_page .admin_gallery_details{
 	max-width: 100%;
 }
 .delete_media_page .admin_gallery_details,  .set_thumbnail_page .admin_gallery_details, .set_caption_page .admin_gallery_details, .edit_gallery_details_page .admin_gallery_details{
 	display: none;
 }
 .admin_gallery_name, .admin_gallery_description, .admin_gallery_filename{
 	width: calc(100% - 10px);
 	text-align: left;
 	margin: 0 5px 3px 5px;
 }
 .admin_gallery_name span, .admin_gallery_description span, .admin_gallery_filename span{
 	font-weight: bold;
 }
 .action_image{
 	clear: both;
 	display: block;
 	width: 100%;
	max-width: 600px;
 	max-height: 600px;
 	padding: 0;
 	margin: 0 auto;
 }
 .action_video_title{
 	text-align: center;
 }
 .action_buttons{
 	text-align: center;
 }
.admin_container .action_message{
	max-width: 100%;
	font-size: 1.2em;
	font-style: italic;
	text-align: center;
	margin: 15px 5px 15px 5px;
}
.admin_container .action_message span{
	text-decoration: underline;
	font-weight: 700;
}
.admin_container .action_buttons{
	margin: 15px 0 0 0;
}
.upload_page .action_buttons{
	margin-top: 35px;
}
.set_caption_page textarea{
	max-width: 600px;
	margin: 15px 5px 15px 5px;
	height: 80px;
}

#uploader{
	width: 100% !important;
	text-align: center !important;
	margin: 0 0 10px 0;
}
.plupload_logo{
	display: none !important;
	background: url('images/plupload.png') no-repeat 0 0 !important;
}
.plupload_header_content{
	padding: 5px 0px 0 5px !important;
	text-align: left !important;
}
.plupload_wrapper{
	min-width: 320px !important;
}
.plupload_container{
	margin: 0 auto !important;
}
.plupload_file_loading .plupload_file_thumb {
	background: #eee url(images/loading.gif) center no-repeat !important;
}
#uploading_message, #upload_complete_message{
	font-size: 1.4em;
	margin: 15px 0 25px 0;
}
#uploading_message span{
	vertical-align: top;
}
#uploading_message img{
	vertical-align: top;
	margin: -10px 0 0 0;
}




/* USERS PAGE
-------------------------------------------------- */
.add_user_container{
	margin: 50px 0 50px 0;
}
.add_user_form{
 	width: 100%;
 	box-sizing: border-box;
 	margin: 0 auto;
 	margin-top: 30px;
 }
 .add_user_form input[type="text"],  .add_user_form input[type="password"], .add_user_form textarea{
 	font-size: 1.1em;
 	padding: 3px 5px 3px 5px;
 	width: calc(100% - 10px);
 	margin: 0 5px 15px 5px;
 	box-sizing: border-box;
 }
 .add_user_form input[type="text"],  .add_user_form input[type="password"], .add_user_form textarea{
 	background: #f7f7f7;
 	border: 1px solid #ccc;
 }
 .add_user_form .add_user_form_line{
 	text-align: center;
 }
 .add_user_form textarea{
 	text-align: left;
 }
 .add_user_form .add_user_form_line label{
 	font-weight: bold;
 	vertical-align: top;
 }
 .user_password_reset_notice{
 	width: 80%;
 	max-width: 550px;
 	text-align: left;
 	font-size: 1.3em;
 	line-height: 110%;
 	margin: 0 auto;
 	margin-top: -10px;
 	margin-bottom: 40px;
 	padding: 5px;
 	background: #f0f0f0;
 	border: 1px solid #ccc;
 }
 .user_password_reset_notice p{
 	margin: 0 5px 0px 5px;
 }
 .user_password_reset_notice span{
 	font-weight: bold;
 }
 .add_user_notice{
	width: 80%;
	max-width: 550pc;
 	text-align: left;
 	font-size: 1.3em;
 	line-height: 110%;
 	margin: 0 auto;
 	margin-top: -10px;
 	margin-bottom: 40px;
 	padding: 5px;
 	background: #f0f0f0;
 	border: 1px solid #ccc;
 }
 .add_user_notice p{
 	margin: 0 5px 0px 5px;
 }
 .add_user_notice span{
 	font-weight: bold;
 }
 .add_user_error{
 	color: red;
 	margin: 30px 0 0 0;
 }
 .add_user_success{
 	color: green;
 	margin: 30px 0 0 0;
 }
 
.user_table{
	width: 100%;
	margin: 0 5px 0 5px;
}
.user_row{
	text-align: left;
}
.user_row span{
	font-weight: bold;
}
.user_row-narrow_item{
	width: 15%;
	min-width: 75px;
	max-width: 350px;
	text-align: left;
	white-space: nowrap;
	padding: 10px 5px 10px 5px;
}
.user_row-wide_item{
	width: 20%;
	min-width: 60px;
	max-width: 250px;
	text-align: left;
	white-space: nowrap;
	padding: 10px 5px 10px 5px;
}
.user_table input[type="text"]{
	width: 80%;
	font-size: 0.9em;
	padding: 1px 3px 1px 3px;
}
.user_table_dark{
	background: #f0f0f0;
}

.collaborator_form{
	margin: 50px 0 50px 0;
}




/* Buttons, inputs
-------------------------------------------------- */
.button {
	display: inline-block;
	background: #ccc;
	border-color: #bbb;
	font: 400 1em "Roboto Condensed", "Lucida Grande","Lucida Sans Unicode", "Lucida Sans", Arial, sans-serif;
	padding: 4px 8px 4px 8px;
	margin: 0 5px 3px 5px;
	white-space: nowrap;
	cursor: pointer;
	background-image: none;
	border: 1px solid transparent;
	border-radius: 4px;
}
.button:hover, .button:focus {
	background: #ccc;
	border-color: #ccc;
	-moz-box-shadow: 0 0 5px rgba(206, 59, 69, .5);
	box-shadow: 0 0 5px rgba(206, 59, 69, .5);
	color: #b13036;
}
input[type="submit"], input[type="reset"] {
	background: #ccc;
	border: 1px solid #ccc;
	font: 400 1em "Roboto Condensed", "Lucida Grande","Lucida Sans Unicode", "Lucida Sans", Arial, sans-serif;
	padding: 4px 8px 4px 8px;
	margin: 0 5px 0 5px;
	cursor:  pointer;
	border-radius: 3px;
}
input:hover[type="submit"], input:focus[type="submit"], input:hover[type="reset"], input:focus[type="reset"] {
	background: #ccc;
	border-color: #bbb;
	color: #b13036;
	-moz-box-shadow: 0 0 8px rgba(206, 59, 69, .5);
	box-shadow: 0 0 8px rgba(206, 59, 69, .5);
}
textarea, input[type="text"] {
	border: 1px solid #ccc;
	border-radius: 2px;
}
textarea:focus, input:focus[type="text"], input:focus[type="submit"] {
	-moz-box-shadow: 0 0 8px rgba(206, 59, 69, .5);
	box-shadow: 0 0 8px rgba(206, 59, 69, .5);
	outline: none;
}

.registrationError {
	float: left;
	width: 100%;
	display: block;
	color : red;
	margin: 30px 0 15px 0;
}
.registrationSuccess {
	float: left;
	width: 100%;
	display: block;
	color : green;
	margin: 30px 0 15px 0;
}
.reg_title {
	text-align: center;
}





/* RESPONSIVE CSS
-------------------------------------------------- */
/*==========  Mobile First Method  ==========*/

/* Custom, Anything smaller than an iPhone */ 
@media only screen and (min-width : 1px) {
	 .add_user_form .add_user_form_line label{
		display: block;
		clear: both;
		text-align: left;
		width: calc(100% - 10px);
		min-width: 200px;
		max-width: 500px;
		margin: 0 auto;
		padding: 5px 0 2px 0;
	}
	.add_user_form input[type="text"],  .add_user_form input[type="password"], .add_user_form textarea{
		min-width: 150px;
		max-width: 500px;
	}
	.admin_form .admin_form_row label{
		display: block;
		clear: both;
		text-align: left;
		width: calc(100% - 10px);
		min-width: 200px;
		max-width: 500px;
		margin: 0 auto;
		padding: 5px 0 0 0;
	}
	.plupload_container{
		width: calc(100% - 10px);
		//min-height: 230px !important;
		//max-width: 320px !important;
	}
	.plupload_header_text{
		max-width: 320px !important;
	}
	.plupload_total_file_size{
		display: none !important;	
	}
	.plupload_stop, .plupload_progress_container, .plupload_droptext{
		display: none !important;
	}
	.notice{
		margin-top: 75px;
		margin-bottom: 90px;
	}
}


/* Custom, iPhone Retina */ 
@media only screen and (min-width : 320px) {
	.plupload_container{
		//min-height: 230px !important;
		//max-width: 320px !important;
	}
	.plupload_header_text{
		max-width: 200px !important;
	}
	.plupload_total_file_size{
		display: none !important;	
	}
	.plupload_stop, .plupload_progress_container, .plupload_droptext{
		display: none !important;
	}
	.notice{
		margin-top: 75px;
		margin-bottom: 90px;
	}
	.admin_container .gallery_listing_tile .admin_options{
		margin: 0 0 20px 0;
	}
	.admin_container .gallery_listing_tile .wordpress_shortcode{
		margin: 10px 0 0 0;
	}
	.admin_container .gallery_listing_tile .admin_file_options_clear{
		display: block;
		width: 100%;
		height: 3px;
		clear: both;
	}
}

/* Extra Small Devices, Phones */ 
@media only screen and (min-width : 480px) {
	.plupload_container{
		//max-width: 440px !important;
		//min-height: 300px !important;
	}
	.plupload_header_text{
		max-width: 100% !important;
	}
	.plupload_droptext{
		display: block !important;
		margin-top: 10px !important;
	}	
}
/* Extra Small Devices, Phones */ 

/* Small Devices, Tablets */
@media only screen and (min-width : 768px) {
 	.add_user_form .add_user_form_line label{
 		display: inline-block;
 		text-align: right;
 		width: 140px;
 		min-width: 140px;
 		max-width: 140px;
 		padding: 5px 0 0 0;
 	}
 	.add_user_form input[type="text"],  .add_user_form input[type="password"], .add_user_form textarea{
 		min-width: 150px;
 		max-width: 300px;
 	}
    .admin_form .admin_form_row label{
    	display: inline-block;
    	text-align: right;
    	width: 150px;
    	min-width: 150px;
    	max-width: 150px;
    	padding: 5px 0 0 0;
    }
    
	.admin_container .gallery_listing_tile{
		width: 98%;
		margin: 0 auto;
		margin-bottom: 50px;
		text-align: left;
		padding: 0;
	}
	.admin_container .gallery_listing_tile .gallery_thumbnail{
		display: inline-block;
		width: 38%;
		vertical-align: top;
		margin-bottom: -4px;
		text-align: center;
	}
	.admin_container .gallery_listing_tile .gallery_thumbnail img{
		max-width: 100%;
		max-height: 220px;
		padding: 0;
		margin: 0;
	}
	.admin_container .gallery_listing_tile .gallery_data{
		display: inline-block;
		width: 60%;
		margin-left: 10px;
		vertical-align: top;
	}
	.admin_container .gallery_listing_tile .gallery_data h3{
		margin: 2px 0 8px 0;
	}
	.admin_container .gallery_listing_tile .gallery_data p{
		margin: 0 0 3px 0;
	}
	.admin_container .gallery_listing_tile .admin_options{
		margin: 10px 0 10px 0;
	}
	.admin_container .gallery_listing_tile .admin_file_options_clear{
		display: none;
	}
	.plupload_container{
		//min-height: 400px !important;
		//max-width: 730px !important;
	}
	.plupload_stop{
		display: inline-block !important;
	}
	.plupload_total_file_size{
		display: inline-block !important;	
	}
	.plupload_progress_container{
		display: block !important;
	}
	.notice{
		margin-top: 120px;
		margin-bottom: 170px;
	}
}

/* Medium Devices, Desktops */
@media only screen and (min-width : 992px) {
	.admin_container{
		width: 992px;
	}
	.admin_container .gallery_listing_tile .gallery_thumbnail{
		width: 34%;
	}
	.admin_container .gallery_listing_tile .gallery_data{
		margin-left: 10px;
	}
	.plupload_container{
		//min-height: 450px !important;
		//max-width: 950px !important;
	}
}

/* Large Devices, Wide Screens */
@media only screen and (min-width : 1200px) {
	.plupload_container{
		//max-width: 1160px !important;
	}
}

/* Extra Large Devices, Extra Wide Screens */
@media only screen and (min-width : 1600px) {
	.plupload_container{
		//max-width: 1560px !important;
	}
}