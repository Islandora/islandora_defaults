# ![Islandora Defaults mascot](https://user-images.githubusercontent.com/2371345/67035035-31828c80-f0ef-11e9-8c46-db902caaaa81.png)
# Islandora Defaults
[![Build Status](https://github.com/islandora/islandora_defaults/actions/workflows/build-2.x.yml/badge.svg)](https://github.com/Islandora/islandora_defaults/actions)
[![Contribution Guidelines](http://img.shields.io/badge/CONTRIBUTING-Guidelines-blue.svg)](./CONTRIBUTING.md)
[![LICENSE](https://img.shields.io/badge/license-GPLv2-blue.svg?style=flat-square)](./LICENSE)

## Future Development

Islandora Defaults is no longer under active development, and is being deprecated. Its core purpose is being (and has been) superseded by a project to create an example Starter Site, which [exists in the `islandora-starter-site` repository](https://github.com/Islandora/islandora-starter-site). You can read the announcement [here](https://groups.google.com/u/1/g/islandora/c/uGzhTnW4TUI).

### Deprecation/Removal Preparation

To ease the removal of this module from existing installations, we have implemented a post-update hook which should remove any references to this module from configuration entities that it installed. To make use of it, it should be sufficient to update to the `islandora/islandora_defaults` package in your Drupal project such that it includes the new hook, and to run it. On the CLI, this might be effected as something like:

```bash
# Navigate to the root of your Composer project; for example:
cd /var/www/html/drupal

# Update islandora/islandora_defaults such that it has the post-update hooks
# available. Presently anticipating a "3.0.1" release to which it should update.
composer require "islandora/islandora_defaults:^3"
# A caveat exists in that, if `islandora/islandora_defaults` is required by any
# other Composer package, then those packages may have to be updated first; for
# example, it is known that there exist Drupal installation profiles that
# specify a dependency on `islandora/islandora_defaults` (such as https://github.com/Islandora-Devops/islandora_install_profile_demo/blob/181a53bb230d7ced6e70e7746f0da567216ebbf7/composer.json#L157),
# which would likely have to receive a treatment to strip out any references
# from their configurations which explicitly bind to `islandora_defaults`
# similar to our update hook, and to include updated requirements accordingly
# in the root Composer project.

# Clear cache (paranoia; to ensure the update hooks are appropriately
# discovered).
drush cr

# Run the update hook.
drush updb

# Now that our config entities should stay behind, the module itself should be
# fine to be uninstalled. Note that this should/may also result in the
# uninstallation of the islandora_oaipmh and islandora_search modules; though,
# the configurations they included should remain in the system.
drush pm-uninstall islandora_defaults

# Perform a dry-run removal to list out all that would be removed, when
# islandora/islandora_defaults is removed, as some modules may only be included
# transitively.
composer remove --dry-run islandora/islandora_defaults

# Add back some of the modules that might just be included in the project
# transitively, for example:
composer require "drupal/field_group:^3" "drupal/field_permissions:^1" \
  "islandora/controlled_access_terms:^2" "islandora/islandora_mirador:^2"
# However, in the event that you indeed are not using some of these modules, you
# might instead proceed to ensure that they are uninstalled from Drupal, similar
# to the "drush pm-uninstall" invocation above. For the version specs to use, it
# may be useful to refer to what the our `composer.json` specifies.

# Remove the package from the project entirely.
composer remove islandora/islandora_defaults

# Clear the caches, for cleanup/paranoia.
drush cr

# Restart your webserver to register the movement of islandora_mirador from
# submodule to a module in its own right; otherwise, caching of class
# definitions might lead to errors. The command might look something like:
sudo systemctl restart apache2
```

<details>
<summary>With output in context, what this might look like</summary>

This was executed on a revived `standard` instance of `islandora-playbook`, with
some very minor preparation to work around the fact that the update hook code
did not yet exist in released code (and so had to point at the development
branch (`dev-fix/config-enforcement`) and made use of aliases accordingly (
`[...] as 3.x-dev`)). When running post-release, the version spec `^3` should be
able to be used instead.

```
vagrant@islandora8:~$ cd /var/www/html/drupal
vagrant@islandora8:/var/www/html/drupal$ composer require "islandora/islandora_defaults:dev-fix/config-enforcement as 3.x-dev"
./composer.json has been updated
Running composer update islandora/islandora_defaults
Loading composer repositories with package information
Info from https://repo.packagist.org: #StandWithUkraine
Updating dependencies
Lock file operations: 1 install, 1 update, 0 removals
  - Upgrading islandora/islandora_defaults (2.1.1 => dev-fix/config-enforcement dac37d2)
  - Locking islandora/islandora_mirador (2.2.1)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 1 install, 1 update, 0 removals
  - Downloading islandora/islandora_mirador (2.2.1)
  - Downloading islandora/islandora_defaults (dev-fix/config-enforcement dac37d2)
  - Installing islandora/islandora_mirador (2.2.1): Extracting archive
  - Upgrading islandora/islandora_defaults (2.1.1 => dev-fix/config-enforcement dac37d2): Extracting archive
Package doctrine/reflection is abandoned, you should avoid using it. Use roave/better-reflection instead.
Package silex/silex is abandoned, you should avoid using it. Use symfony/flex instead.
Package symfony/debug is abandoned, you should avoid using it. Use symfony/error-handler instead.
Package webmozart/path-util is abandoned, you should avoid using it. Use symfony/filesystem instead.
Generating autoload files
68 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
No security vulnerability advisories found
vagrant@islandora8:/var/www/html/drupal$ drush cr
 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
 [success] Cache rebuild complete.
vagrant@islandora8:/var/www/html/drupal$ drush updb
 -------------------- ------------- ------------- ---------------------------
  Module               Update ID     Type          Description
 -------------------- ------------- ------------- ---------------------------
  islandora_defaults   remove_enfo   post-update   Remove "enforced"
                       rced_depend                 dependency on this module
                       ency                        from installed config.
 -------------------- ------------- ------------- ---------------------------


 Do you wish to run the specified pending updates? (yes/no) [yes]:
 >

>  [notice] Update started: islandora_defaults_post_update_remove_enforced_dependency
>  [notice] Update completed: islandora_defaults_post_update_remove_enforced_dependency
>  [warning] Illegal string offset 'label' OaiPmh.php:116
>  [warning] Illegal string offset 'value' OaiPmh.php:116
>  [warning] Illegal string offset 'label' OaiPmh.php:116
>  [warning] Illegal string offset 'value' OaiPmh.php:116
 [success] Finished performing updates.
vagrant@islandora8:/var/www/html/drupal$ drush pm-uninstall islandora_defaults
The following extensions will be uninstalled: islandora_defaults, islandora_oaipmh, islandora_search

 Do you want to continue? (yes/no) [yes]:
 >

 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
 [success] Successfully uninstalled: islandora_defaults, islandora_oaipmh, islandora_search
vagrant@islandora8:/var/www/html/drupal$ composer require "drupal/field_group:^3" "drupal/field_permissions:^1"   "islandora/controlled_access_terms:^2" "islandora/islandora_mirador:^2"
./composer.json has been updated
Running composer update drupal/field_group drupal/field_permissions islandora/controlled_access_terms islandora/islandora_mirador
Loading composer repositories with package information
Updating dependencies
Nothing to modify in lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 0 updates, 0 removals
Package doctrine/reflection is abandoned, you should avoid using it. Use roave/better-reflection instead.
Package silex/silex is abandoned, you should avoid using it. Use symfony/flex instead.
Package symfony/debug is abandoned, you should avoid using it. Use symfony/error-handler instead.
Package webmozart/path-util is abandoned, you should avoid using it. Use symfony/filesystem instead.
Generating autoload files
68 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
No security vulnerability advisories found
vagrant@islandora8:/var/www/html/drupal$ composer remove islandora/islandora_defaults
./composer.json has been updated
Running composer update islandora/islandora_defaults
Loading composer repositories with package information
Updating dependencies
Lock file operations: 0 installs, 0 updates, 1 removal
  - Removing islandora/islandora_defaults (dev-fix/config-enforcement dac37d2)
Writing lock file
Installing dependencies from lock file (including require-dev)
Package operations: 0 installs, 0 updates, 1 removal
  - Removing islandora/islandora_defaults (dev-fix/config-enforcement dac37d2)
Deleting web/modules/contrib/islandora_defaults - deleted
Package doctrine/reflection is abandoned, you should avoid using it. Use roave/better-reflection instead.
Package silex/silex is abandoned, you should avoid using it. Use symfony/flex instead.
Package symfony/debug is abandoned, you should avoid using it. Use symfony/error-handler instead.
Package webmozart/path-util is abandoned, you should avoid using it. Use symfony/filesystem instead.
Generating autoload files
68 packages you are using are looking for funding.
Use the `composer fund` command to find out more!
No security vulnerability advisories found
vagrant@islandora8:/var/www/html/drupal$ drush cr
 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
 [success] Cache rebuild complete.
vagrant@islandora8:/var/www/html/drupal$ sudo systemctl restart apache2
vagrant@islandora8:/var/www/html/drupal$
```

The warnings:

```
 [warning] Illegal string offset 'label' OaiPmh.php:116
 [warning] Illegal string offset 'value' OaiPmh.php:116
```

... are from `islandora_defaults` shipping configuration for an older schema
([commit which changed it](https://git.drupalcode.org/project/rest_oai_pmh/-/commit/784d827eb77cd2513b66054b31b7dfae54f469c2))
of the [REST OAI-PMH module](https://www.drupal.org/project/rest_oai_pmh), of
which it is outside the scope of present efforts to address.

</details>

---

## Introduction

Islandora Defaults is a Drupal feature intended to showcase all of the functionality of a full Islandora install. It provides custom content types, contexts, viewers, and other 'starter' configuration the reflects a baseline Islandora site. It is expected that you will take these defaults and customize them further to fit your use case.

## Installation
Islandora_defaults is a Drupal Feature. See [Drupal documentation on Features](https://www.drupal.org/docs/8/modules/features) for more information.

## Configuration
Islandora_defaults is itself a set of configurations for Drupal. It can be further customized by customizing content types, actions, and other aspects of Drupal that are configured by islandora_defaults.

## Maintainers

Current maintainers:

* [Seth Shaw](https://github.com/seth-shaw-unlv)

## Documentation

Further [documentation for this module](https://islandora.github.io/documentation/reference/islandora_defaults_reference/) is available on the [Islandora documentation site](https://islandora.github.io/documentation/).

## Troubleshooting/Issues

Having problems or solved a problem? Check out the Islandora Google Groups for a solution.

* [Islandora Group](https://groups.google.com/forum/?hl=en&fromgroups#!forum/islandora)

## Development
If you would like to contribute, please get involved by attending our weekly [Tech Call](https://github.com/Islandora/islandora-community/wiki/Weekly-Open-Tech-Call). We love to hear from you!

If you would like to contribute code to the project, you need to be covered by an Islandora Foundation [Contributor License Agreement](http://islandora.ca/sites/default/files/islandora_cla.pdf) or [Corporate Contributor License Agreement](http://islandora.ca/sites/default/files/islandora_ccla.pdf). Please see the [Contributors](http://islandora.ca/resources/contributors) pages on Islandora.ca for more information.

We recommend using either [ISLE-DC](https://github.com/Islandora-Devops/isle-buildkit) or the [islandora-playbook](https://github.com/Islandora-Devops/islandora-playbook) to get started.

## License

[GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)
