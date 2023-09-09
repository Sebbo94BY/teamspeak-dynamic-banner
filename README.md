# TeamSpeak Dynamic Banner

[![PHPUnit](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/actions/workflows/phpunit.yml/badge.svg?branch=main)](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/actions/workflows/phpunit.yml?branch=main)
[![Code-Style](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/actions/workflows/phpcsfixer.yml/badge.svg?branch=main)](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/actions/workflows/phpcsfixer.yml?branch=main)
[![Code-Coverage](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/actions/workflows/coverage.yml/badge.svg?branch=main)](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/actions/workflows/coverage.yml?branch=main)
[![Codecov.io-Coverage](https://codecov.io/gh/Sebbo94BY/teamspeak-dynamic-banner/branch/main/graph/badge.svg?token=13U058HU8P)](https://codecov.io/gh/Sebbo94BY/teamspeak-dynamic-banner)

I first played around with building dynamic banners for TeamSpeak in 2015. After gaining some knowledge, however, it was not pushed further. In 2018 I re-tackled the topic again, as it was a project for a small hosting company, but due to unresolved technical questions it was never released. Then in 2023 I decided to revive the project and release it on Github as an OpenSource project. And here we are now.

Usually you configure a hostbanner Gfx URL on your TeamSpeak server to show your users a beautiful banner with e.g. announcements or the current time on it.

This project extends your configured banner by allowing you to configure as many images (here called templates) as you want and each of those templates can have one or more dynamic texts on it.

Just a few examples:

- Dynamically greet your TeamSpeak clients in your banner with e.g. `Hello Max`
- Dynamically add the current date and/or time to your banner
- Dynamically show on your banner the amount of supporters, which are currently online

Be a [stargazer](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/stargazers), star it!


## Donations

**TeamSpeak Dynamic Banner** is free software and is made available free of charge. Your donation, which is purely optional, supports me at improving the software as well as reducing my costs of this project. If you like the software, please consider a donation. Thank you very much!

[Donate with PayPal](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7ZRXLSC2UBVWE)


## Features

* Self-hosted / On-Premise
    * You have the full control about the application
    * Nobody can tell you, that you are not allowed to use it
    * It's OpenSource, so free-of-charge
* Supported instances: TeamSpeak 3 server, TeamSpeak 5 server
* Add one or more instances as data source
    * ServerQuery credentials are stored encrypted in the database
    * Supports the ServerQuery protocol RAW and SSH
    * Define the client nickname
    * Define the default channel, where the client should be in when connected
* Upload one or more images as templates
* Define one or more banners (each gets a dedicated link)
    * Supports various rotation configurations
        * Non-Random: Every client, which requests the banner, will always see the same template as all other clients.
        * Random: Every client, which requests the banner, gets a random, maybe different template shown.
* Add and configure one or more templates for each banner
    * Configure (different) URL redirects for each banner (e.g. one banner redirects to your homepage, the other to your social media account)
    * Over 140 standard variables (e.g. current time, count of online clients, client nickname, etc.)
    * Use your preferred TrueType Font (TTF)
    * Set the font size for your texts
    * Set the font angel for your texts
    * Set the font color (RGB) for your texts
    * Optionally disable a specific template in a banner configuration if you don't want to delete it's configuration and is only necessary temporary for announcements or events for example
* The ServerQuery client stays 24x7 connected on your TeamSpeak server
    * This avoids annoying connects / disconnects to get all necessary data every time
    * The required data gets fetched event based (e.g. client joins the server) and partitially regulary (e.g. every 5 minutes)
    * All fetched data will be stored in the Redis to speed up the entire application


## Supported Languages

This project has multi-language support for the UI.

For a current list of supported languages, please check the respective folder: [laravel/lang/](/laravel/lang/)


## Matomo Tracking

If you're interested in some statistics of this application, you can configure a Matomo tracking code, which sends the respective tracking data to your Matomo installation.

Simply configure the Matomo tracking in your DotEnv file (`.env`) with the respective variables. See the configuration file `laravel/config/matomo.php` for the available environment variables and a detailed explanation.

After configuring it, don't forget to cache your configuration: `php artisan config:cache`


## Requirements

### Hardware (minimum)

> **Note**
>
> Depending on the configured and running TeamSpeak instance bots, available servergroups, currently connected clients on your TeamSpeak server(s) and the amount of clients which are requesting the banner(s), you might need more CPU cores and/or memory.
>
> In average, every TeamSpeak instance bot requires 50 MB memory while it is actively running.
> 
> Additional, you should calculate with another 50 MB memory for the actual application to e. g. generate the new rendered banners or when an administrator clicks through the application and changes configurations.
>
> So 128 MB memory should be sufficient in the most cases, but your operating system and other process also require some memory, so you should rather have at least 256 MB memory.

* 1 CPU Core
* 256 MB Memory

### Software

* Web server (e.g. apache, nginx)
* PHP 8.1 or 8.2 with the following extensions:
    * see [Laravel PHP requirements](https://laravel.com/docs/10.x/deployment#server-requirements)
    * ssh2 (if you want to connect your instance via SSH)
    * gd
* Database (see [supported databases by Laravel](https://laravel.com/docs/10.x/database#introduction))
* [Redis](https://redis.io/)
* Git
* [Composer](https://getcomposer.org/)
* [npm](https://www.npmjs.com/) (Version 9.x or newer)


## TeamSpeak Permissions

This project requires the following permissions on your TeamSpeak server:

| Permission Name | Description |
|-----------------|-------------|
| `i_client_max_clones_uid` | Should be 2 or higher. One for connecting 24x7 and one for testing configuration changes. |
| `b_serverquery_login` | Allow the ServerQuery to login |
| `b_virtualserver_channel_list` | Get the channel list to be able to configure a default channel for the bot. |
| `b_channel_join_*` | The respective permission to allow the bot to join the configured default channel. |
| `i_channel_join_power` | The join power, which the bot should have. |
| `i_client_move_power` | The move power, which the bot should have to move itself into the default channel. |
| `b_virtualserver_notify_register` | Allow the bot to register and listen for TeamSpeak server events. |
| `b_virtualserver_notify_unregister` | Allow the bot to unregister from TeamSpeak server events. |
| `b_virtualserver_info_view` | Allow the bot to get some information. |
| `b_virtualserver_connectioninfo_view` | Allow the bot to get some information. |
| `b_virtualserver_client_list` | Allow the bot to get some information. |
| `b_virtualserver_client_dbinfo` | Allow the bot to get some information. |
| `b_virtualserver_servergroup_list` | Allow the bot to get some information. |
| `b_virtualserver_servergroup_client_list` | Allow the bot to get some information. |
| `b_client_info_view` | Allow the bot to get some information. |
| `b_client_remoteaddress_view` | Allow the bot to get the IP addresses of the clients. This is technically necessary to identify users when they request the banner. |


## Architecture

[Open Architecture README](/docs/ARCHITECTURE.md)


## Installation

[Open Installation README](/docs/INSTALLATION.md)


## Update

[Open Update README](/docs/UPDATE.md)


## Backup

If you want to regulary backup this application, you should backup the following things:

* `.env` file (or only the `APP_KEY` from this file)
* Database
* `laravel/public/uploads/` directory


## Feature Request? Bug Report?

Open a Github issue [here](https://github.com/Sebbo94BY/teamspeak-dynamic-banner/issues/new)!


## Contribute

[Open Translation README](/docs/TRANSLATION.md)

[Open Contribute README](/docs/CONTRIBUTE.md)
