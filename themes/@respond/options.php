<?php

//This is how many rows of galleries we will display in the gallery directory
theme_setting("galleries_per_page", 10); //required by core

//this is the number of columns that each thumbnail page will have
theme_setting("gallery_images_per_page", 16); //required by core

//thumbnail image width (leave height or width=0 for autosizing)
theme_setting("thumbnail_width", 500); //required by core

//thumbnail image width (leave height or width=0 for autosizing)
theme_setting("thumbnail_height", 375); //required by core

//Set this to the maximum width for images to display at.
theme_setting("image_display_width", 1600); //required by core

//Set this to the maximum height for images to display at.
theme_setting("image_display_height", 1000); //required by core

//enable admin lightbox overlay
theme_setting("enable_ajax", false); //required by core

//display gallery title on gallery index
theme_setting("show_gallery_title", true);

//display gallery description on gallery index
theme_setting("show_gallery_description", true);

//display gallery poster name on gallery index
theme_setting("show_poster_name", true);

//display gallery post date on galery index
theme_setting("show_posted_date", true);

//the maximum length permitted for gallery titles
theme_setting("max_title_length", 50); //required by core

//the maximum length permitted for gallery descriptions
theme_setting("max_description_length", 195); //required by core

//display gallery post date on galery index
theme_setting("show_share_button", true);

?>
