ed_password_policy
========================

Password Policy Password for Department of Education

This module implements the standards as set by the document:

Standard PR.AC 3: User-
Notification/Warning Banner
Approval Date:
January 29, 2019

and

Department of Education Password Parameter Policy and Procedures
Supplement 1 to Department of Education,
IAS-01 Logical Access Control Guidance
Version Date: March 31, 2017

This module relies on installing and configuring contrib modules, as well as
modifying the login form.

The following modules are used:

password_policy
password_policy_character_types
password_policy_characters
password_policy_consecutive
password_policy_history
password_policy_length
password_policy_username
legal
password_policy_delay
password_policy_systemname
password_policy_recycle
login_history
login_security

NOTES:
These are at their current production versions, however, patches have to be
applied to the password_policy module:

https://www.drupal.org/files/issues/2019-03-07/password_policy-use-core-temp-store-3032549-8.patch
https://www.drupal.org/files/issues/2019-03-04/password_policy-use-core-temp-store-3032549-3.patch
https://www.drupal.org/files/issues/2018-07-30/password_policy-config_import_field_error-2771129-57.patch
https://www.drupal.org/files/issues/password_policy-inconsistent-datetime-formats-2860671-13-D8.patch
https://www.drupal.org/files/issues/password_policy-2862906-2.patch

Here is what the composer.json can look like:

"extra": {

    "installer-paths": {

        "web/core": ["type:drupal-core"],

        "web/libraries/{$name}": ["type:drupal-library"],

        "web/modules/contrib/{$name}": ["type:drupal-module"],

        "web/profiles/contrib/{$name}": ["type:drupal-profile"],

        "web/themes/contrib/{$name}": ["type:drupal-theme"],

        "drush/contrib/{$name}": ["type:drupal-drush"]

    },

    "patches": {

        "drupal/password_policy": {

            "2860671 - Inconsistent date format and timezone usage leads to infinite password resets.": "https://www.drupal.org/files/issues/password_policy-inconsistent-datetime-formats-2860671-13-D8.patch",

            "2771129 - Field field_last_password_reset is unknown fix": "https://www.drupal.org/files/issues/2018-07-30/password_policy-config_import_field_error-2771129-57.patch",

            "2867320 - Password Policy Recycle Drupal Module": "https://www.drupal.org/files/issues/2867320-4-password-recycle.patch",

            "3032549 - replaces references to Drupal user SharedTempStoreFactory with Drupal Core TempStore SharedTempStoreFactory": "https://www.drupal.org/files/issues/2019-03-04/password_policy-use-core-temp-store-3032549-3.patch",

            "2862906 - Remove 'bypass password policies' permission": "https://www.drupal.org/files/issues/password_policy-2862906-2.patch"

        }

    }

}

NOTE: password_policy_recycle is created via this patch: https://www.drupal.org/files/issues/2867320-4-password-recycle.patch

The systemname will probably need to be updated in:
password_policy_systemname/src/Plugin/PasswordConstraint/PasswordSystemname.php
We could add an admin screen for this or just keep adding to an array.

Future changes of the warning text or required password paramaters can
be implemented via new version of this module.

IMMEDIATE UPDATES OF WARNING AND POLICY
In the case of needing to change them immediately to comply with a law or
special situation, these admin pages can be used:

Password policies: /admin/config/security/password-policy
Warning: /admin/config/people/legal
