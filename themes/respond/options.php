<?php

//This is how many rows of galleries we will display in the gallery directory
theme_setting("galleries_per_page", 12); //required by core

//this is the number of columns that each thumbnail page will have
theme_setting("gallery_images_per_page", 20); //required by core

//thumbnail image width (leave height or width=0 for autosizing)
theme_setting("thumbnail_width", 500); //required by core

//thumbnail image width (leave height or width=0 for autosizing)
theme_setting("thumbnail_height", 375); //required by core

//Set this to the maximum width for images to display at.
theme_setting("image_display_width", 1024); //required by core

//Set this to the maximum height for images to display at.
theme_setting("image_display_height", 800); //required by core

//enable admin lightbox overlay
theme_setting("enable_ajax", true); //required by core

//display gallery title on gallery index
theme_setting("show_gallery_title", true);

//display gallery description on gallery index
theme_setting("show_gallery_description", true);

//display gallery poster name on gallery index
theme_setting("show_poster_name", false);

//display gallery post date on galery index
theme_setting("show_posted_date", true);

//the maximum length permitted for gallery titles
theme_setting("max_title_length", 150); //required by core

//the maximum length permitted for gallery descriptions
theme_setting("max_description_length", 250); //required by core

//display gallery post date on galery index
theme_setting("show_share_button", false);

?>
