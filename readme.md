# Welcome to cimplems (Discontinued)

This project has been discontinued since 2021.

## Installation Instructions

1. **Clone or Download:**
   * **Clone the repository:** If you have Git installed, run the following command in your terminal:
      ```bash
      git clone https://github.com/tcja/cimplems.git
      ```
   * **Download the ZIP:** Alternatively, download the project as a ZIP file from GitHub and extract it to your desired location.

2. **Install Dependencies:**
   * Open a terminal or command prompt and navigate to the project's root directory.
   * Run the following command to install the required PHP dependencies using Composer:
      ```bash
      composer install
      ```

3. **Configuration:**

   * **App Key:** Generate an application key for encryption and security purposes:
      ```bash
      php artisan key:generate
      ```
   * **Environment Variables:**  Open the `.env` file (create one if it doesn't exist by copying `.env.example`) and make the following changes:
      * `APP_ENV=local` to `APP_ENV=production`
      * `APP_DEBUG=true` to `APP_DEBUG=false`


**Default login credentials:**

* **Email:** admin@admin-cimplems.com
* **Password:** admin

Please change these upon your first login for security reasons. This email will also be used to send you information when someone fills out the contact form.

## What is cimplems?

Cimplems (Simple CMS) is a content management system designed for simplicity. It features:

* **Easy installation:** Utilizes an XML flat file database.
* **User-friendly interface:** WYSIWYG administration with all actions on a single page.

With cimplems, you can create standard pages, manage multiple galleries, and interact with visitors through the contact page. All pages can be toggled ON or OFF, and putting the home page offline activates "under construction" mode.

## How does it work?

Hover over each button to see its function and experiment to get familiar with the available features.

## What's the tech stack?

* **Back-end:** Laravel 8
* **Front-end:** jQuery and Bootstrap

The CMS is reactive by default but can be customized in the config file (`root_website_dir/config/site.php`).

## Languages and Themes

* **Languages:** Localization available for English and French (default configurable in the config file). Create your own language files in `root_website_dir/resources/lang/`.
* **Themes:** Three bootstrap themes are available: "bootstrap" (default), "default" (simple), and "blog". Create your own themes in `root_website_dir/resources/views/themes/` and `root_website_dir/public/themes/`.

## Where are the files stored?

* **Default pages:** `root_website_dir/storage/app/database/pages/default/`
* **Custom pages:** `root_website_dir/storage/app/database/pages/custom/`
* **User admin file:** `root_website_dir/storage/app/database/users/users.xml`
* **Galleries:** `root_website_dir/storage/app/database/galleries/`
* **Gallery images:** `root_website_dir/storage/app/public/images_gallery/`

## License

This project is licensed under the MIT License. © 2019 tcja
