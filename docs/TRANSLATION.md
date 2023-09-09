# TRANSLATION

This documentation describes, how the translation (localization) is implemented in this project.


## Fallback Locale

The default and fallback language (locale) is defined in [`config/app.php`](/laravel/config/app.php): `fallback_locale`

This will be used when e. g. no user is authenticated during the HTTP request or when there does not exist a translation for the target language (locale) for a specific text. So instead of showing variable names, the fallback language (locale) will be presented.


## Directory Structure

All available languages (locales) are defined in [`laravel/lang/`](/laravel/lang/).

The database seeder `LocalizationSeeder` ([LocalizationSeeder.php](/laravel/database/seeders/LocalizationSeeder.php)) defines which languages will be available in the user profile.

Each available language has a dedicated directory with multiple subdirectories and PHP files, which hold and define the actual translations.

Those subdirectories and PHP files should reflect the same structure as the actual views under [`laravel/resources/views/`](/laravel/resources/views/).


## Further Information

This project is using the standard Laravel localization, so you can check out the official documentation for further information: https://laravel.com/docs/10.x/localization
