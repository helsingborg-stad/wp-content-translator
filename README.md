# Wp Content Translator

Minimalistic content translation in WordPress.

## Hooks

### Admin menu

#### ```wp-content-translator/before_add_admin_menu_item```
**Action.** Runs before we add the language item to the admin menu.

#### ```wp-content-translator/after_add_admin_menu_item```
**Action.** Runs after we have added the language item to the admin menu.

### Install language

#### ```wp-content-translator/before_install_language```
**Action:** Runs before language is installed.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

#### ```wp-content-translator/after_install_language```
**Action:** Runs before language is installed.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

#### ```wp-content-translator/should_download_wp_translation_when_installing```
**Filter:** Wheateer or not to download WP translation when installing a new language.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

### Uninstall language

#### ```wp-content-translator/before_uninstall_language```
**Action.** Runs before language removal.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language object

#### ```wp-content-translator/after_uninstall_language```
**Action.** Runs after language removal but before completion redirect.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language objec

#### ```wp-content-translator/should_drop_table_when_uninstalling_language```
**Filter.** Tells if tables of the deleted language should be dropped or not.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language objec

#### ```wp-content-translator/redirect_after_uninstall_language```
**Filter.** Url to redirect to after language is removed.

**Params:**

- *string* $code: Language code (ex: sv_SE)
- *Language* $lang: The language objec
