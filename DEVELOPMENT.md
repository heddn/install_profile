# Instructions for local development

This project uses a [Composer](https://getcomposer.org/) based workflow to
manage [Drupal](https://www.drupal.org/project/drupal) and its dependencies. It
also includes configuration to use [DDEV-Local](https://ddev.readthedocs.io/) for local
development. DDEV itself depends on
[Docker](https://www.docker.com/products/docker-desktop).

## Requirements

* Composer

Get Composer from https://getcomposer.org/download/ You might want to move the `composer.phar` file to a location that is globally executable. For example:

```
mv composer.phar /usr/local/bin/composer
```

To verify Composer has been installed, execute this command: `composer --version`

* DDEV

Make sure your local environment meets [DDEV's system requirements](https://ddev.readthedocs.io/en/latest/#system-requirements) which include [Docker](https://www.docker.com/products/docker-desktop). Then, follow the [getting started guide](https://ddev.com/get-started/) to install DDEV. For Linux/macOS users this can be done using [Homebrew](https://ddev.readthedocs.io/en/stable/#homebrewlinuxbrew-macoslinux).

```
brew tap drud/ddev && brew install ddev
```

To verify DDEV has been installed, execute this command: `ddev --version`

If installing for the first time, you might have to execute the following command for local HTTPS certificates to work:

`mkcert -install`

For operating system specific instructions, refer to the official [installation instructions](https://ddev.readthedocs.io/en/latest/#installation).

## Installing Drupal

* Clone the repository

```
git clone git@github.com:heddn/install_profile.git
```

* Install the project dependencies.

```
cd install_profile
ddev composer install
```

* Start DDEV

```
ddev start
```

This operation may take a while if Docker images needs to be downloaded. At the end of this process, the site can be reached at https://install-profile.ddev.site.

* Install Drupal

The project includes a script that will install Drupal from existing configuration files. Execute the following command:

```
ddev composer si
```

* To generate an administration login, execute this command:

```
ddev exec drush uli
```

## Default content exporting

TL;DR; put UUID of the content item in `custom_default_content.info.yml`, then `drush dcem custom_default_content`

Longer version:
1. Enable default_content module
1. `drush dce node {nid}` (replace node {nid} with the appropriate entity_type and ID as appropriate) grab UUID, then  populate in .info.yml
1. `drush dcem custom_default_content`

## Custom theme

Refer to the theme's [README.txt](web/themes/custom/custom_tw/README.txt) for instructions.
