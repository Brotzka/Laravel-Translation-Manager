[![Latest Stable Version](https://poser.pugx.org/brotzka/Laravel-Translation-Manager/v/stable)](https://packagist.org/packages/brotzka/Laravel-Translation-Manager)
[![Total Downloads](https://poser.pugx.org/brotzka/Laravel-Translation-Manager/downloads)](https://packagist.org/packages/brotzka/laravel-translation-manager) 
[![Latest Unstable Version](https://poser.pugx.org/brotzka/Laravel-Translation-Manager/v/unstable)](https://packagist.org/packages/brotzka/laravel-translation-manager) 
[![License](https://poser.pugx.org/brotzka/Laravel-Translation-Manager/license)](https://packagist.org/packages/brotzka/laravel-translation-manager)


# Laravel Translation Manager

This package provides an easy way to manage your translations in a database. It takes all files from your default locale folder (e.g. ``resources/lang/de``) and creates translation-groups (e.g. ``auth.php`` becomes translation-group ``auth``) which are stored in ``translation_groups``-table. Then every entry from each file will be saved to the ``translations``-table (yes, it takes care of multidimensional arrays of every depth).

After you have finished translating, the package writes all entries back to the resource-folder. All entries will be kept in database, so you can keep translating.

Updates are handled one-way. That means that changes which are made to a file will not replace the value in the database. The other way round, the complete content of a translation-file will be replaced by the values from the database.

## Pros:
- Laravels default translation-loader will not be replaced, so everything keeps working
- Works with every Laravel version (5.*)
- To add a new language, simply add it to your ``config/app.php`` and re-run ``php artisan translations:toDatabse``, make your translations and run ``php artisan translations:toFile``
- No more database exports! Develop locally without using the export function. Simply use your default language.


## Installation
 - require via composer ```composer require brotzka/translation-manager```
 - add list of available languages to your ``config/app.php``:
 ```
 'available_locales' => ['de', 'en', 'sv'],
 ```
 - add service provider to ```config/app.php``` providers-array:
 ```
    /*
     * Package Service Providers...
     */
    // other service provider...
    Brotzka\TranslationManager\TranslationManagerServiceProvider::class,
 ```
 - run migration ```php artisan migrate```

## Commands

### translations:toDatabase
Call via:
```
php artisan translations:toDatabase
```
Collects all files and entries from within your ```resources/lang/``` folder and generates translations-groups and translations and writes them to the database. Existing files will NOT be updated.

### translations:toFile
Call via:
```
php artisan translations:toFile
```
Takes all entries from the database, generates missing language folders and translation-group files and puts the values to the files.

_**NOTE**_: If you want to call the commands via ``Artisan::call('translations.toDatabase')``, you have to register both commands in ``app/Console/Kernel.php``:
```
protected $commands = [
    // ..  other commands
    \Brotzka\TranslationManager\Module\Console\Commands\TranslationToDatabase::class,
    \Brotzka\TranslationManager\Module\Console\Commands\TranslationToFile::class,
];
```

## Usage
In the back, this package creates two more models (``TranslationGroup`` and ``Translation``) tables (``translation_groups`` and ``translations``).

You can use them as you are used to use models in Laravel. The relevant namespace is: ``Brotzka\TranslationManager\Module``. 

You can query relationships like this:
- ``$translation->getParent``: returns the parent-instance if existing (NULL if not)
- ``$translation->children``: returns all children-instances
- ``$translation->getGroup``: returns the translation-group of the current translation
- ``$translationGroup->entries``: returns all entries belonging to the current translation-group


## Future-Plans
- handle JSON-files
- provide some GUI-elements (e.g. language-switcher, translation-manager)
