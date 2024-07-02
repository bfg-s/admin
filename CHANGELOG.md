# Change Logs

All notable changes to this project will be documented in this file.

This project adheres to [Semantic Versioning](http://semver.org/).

## [6.4.0](https://github.com/bfg-s/admin/compare/6.3.0...6.4.0) - 2024-07-02
* Added creation of interactive dashboards with the ability to personalize for different users.
* Added modal use method for deferred loading.
* Fixed checkboxes in realtime model table.
* Removed all "addClass" methods from the components.
* Added REST API for the admin panel.
* Fixed uk translation for login page.
* Added tabs component refactor.
* Added REST API for the admin panel.
* Added tests for the REST API.
* Added tests for the admin panel.
* Added loadeble component, a component that accepts a function and loads the returned content after the page has loaded.
* Added update manager.
* Added change event for the component.
* Added private note widget for the dashboard.
* Added sort by relations in orderBy for select2. 

## [6.3.0](https://github.com/bfg-s/admin/compare/6.2.3...6.3.0) - 2024-06-03

* Added realtime for components
* Added types for all properties and methods.
* Added commentaries for all properties, methods and classes.
* Added code refactor.
* Added browser list to user profile.
* Changed installation command for generate classes with types and commentaries.
* Changed rating stars in model table decoration.
* Removed unused files.
* Fixed extension manager edit package.
* Fixed refresh modal button size.
* Fixed user profile activity.

## [6.2.3](https://github.com/bfg-s/admin/compare/6.2.2...6.2.3) - 2024-05-13

* Fixed bug in call backs.
* Fixed extension create, setup extension with "dev-main"
* Fixed documentation url in the navigation.
* Fixed decrypt old language.
* Added check on status 500 referer in the SystemController.
* Added select delegates for default delegate helpers (statisticBody, nestedModelTable, sortedModelTable). [issue](https://github.com/bfg-s/admin/issues/1)
* Added check on exists default id, created_at and updated_at. [issue](https://github.com/bfg-s/admin/issues/1)
* Changed remove lte publish theme.
* Changed color of restore button from warning to success.

## [6.2.2](https://github.com/bfg-s/admin/compare/6.2.1...6.2.2) - 2024-05-02

* Fixed bug in resource controller when model set in the method.
* Fixed excel export for the model table component.
* Fixed optimized classes imports.
* Fixed reformat code.
* Fixed remove old generator.
* Fixed prepare export for the model table component.
* Fixed menu in modal.
* Fixed table for modal body.
* Fixed search form with default values.
* Added factory running if exists in the model.
* Added modal info page.
* Added percent formatter for model table. 
* Added new method "loadModelBy" for load ChartJs.
* Changed dashboard load chart js with new method.
* Changed statisticBody with new method.
* Changed sortedModelTable with new method.
* Changed sizes for statisticBody and sortedModelTable.

## [6.2.1](https://github.com/bfg-s/admin/compare/6.2.0...6.2.1) - 2024-04-26

* Fixed for the `ImageBrowser` component when empty array.
* Fixed editable component save.

## [6.2.0](https://github.com/bfg-s/admin/compare/6.1.2...6.2.0) - 2024-04-21

* Added new component `ModelCards` for the administrators list.
* Fixed json_decode with right associative.
* Fixed image_browser null value in component.
* Fixed image_browser block after flash_document.
* Fixed callbacks with empty parameters.

## [6.1.2](https://github.com/bfg-s/admin/compare/6.1.1...6.1.2) - 2024-04-11

* Added fine-tuning the default model table component in admin configs.
* Added fine-tuning the default nested component in admin configs.
* Added fine-tuning the default timeline component in admin configs.
* Added strict types for all classes.
* Added tests.
* Fixed lang vertical align for form fields.
* Fixed tabs of Lang component in model relation.
* Fixed hidden order field in model relation ordered mode.
* Fixed ukrainian language renamed from `ua` to `uk`.
* Fixed modal load content.
* Fixed modal form submit.
* Fixed image browser component, the picture review didn't work.
* Fixed ide-Helper for macroable.
* Fixed isModelInput for controller.
* Fixed AmountInput mask, without commas.
* Fixed depth in system nestable_collapse. 
* Fixed info for input wrap in lang.

## [6.1.1](https://github.com/bfg-s/admin/compare/6.1.0...6.1.1) - 2024-04-02

* Added numerated ids for input fields is field if arrayable.
* Added documentation button in header for root users.
* Changed, removed all unnecessary links that remained from the removed functionality.
* Fixed live loads document to form data.
* Fixed your own controller for `Select2` for individual loading of option lists.
* Fixed `Select2` load with form data.
* Fixed `withCollection` in component.
* Fixed `ModelRelation` deep template for nested `ModelRelation` components.

## [6.1.0](https://github.com/bfg-s/admin/compare/6.0.0...6.1.0) - 2024-03-30

In this version, a lot was added, a little changed and a little corrected.

* Added laravel 11 support.
* Added multi nested for model relation component.
* Added image browser for form component.
* Added percent input for form component.
* Added order by for `Select2` load component option.
* Added extension provider helpers for extend the core.
* Added IDE helpers for extensions in navigation.
* Added macroable for all default delegates and helper for them.
* Added tests.
* Changed download excel and csv notification has been changed.
* Changed refactor base controller static properties.
* Changed, remove header `Extensions` in the navigation.
* Changed, remove delegates by default.
* Fixed breadcrumbs for the navigation have been fixed.
* Fixed masks for amount and numeric fields have been fixed.
* Fixed duplication in form input.
* Fixed access denied for the navigation has been fixed.
* Fixed admin user profile has been fixed.

## [6.0.0](https://github.com/bfg-s/admin/compare/5.5.7...6.0.0) - 2024-03-15

A massive update that includes many changes. 
The approach to the interface has been redesigned, new functions have been added, and errors have been fixed.
Backward compatibility with the fifth version is not broken.

* Added theme support.
* Added dockblocks for all classes and methods.
* Added type hinting for all classes and methods.
* Added new component accordion.
* Added new lang wrapper for fields with translater.
* Added use Vue.js in navigation nav bar.
* Added support for Alpine.js. All components that were previously in Vue have now been rewritten in Alpine.js. 
* Changed the main javascript file has been reworked, now there is no dependency on the lar/ljs package.
* Changed new architecture of interface components, now all components use the standard Blade template engine instead of lar/taggable, which significantly increases the performance of the panel.
* Changed the navigation core has been redesigned; the lar/roads package is no longer used.
* Changed, removed dependency on the lar/layout package.
* Changed select2 format for outputting options, now you can use any output format for options, with any model field and/or connection.
* Changed TargetBlank for links, you can now specify in the navigation settings that the link should open in a new tab.
* Changed ImageInput and FileInput can now be used in multi mode with the ability to load multiple files.
* Changed image modifier for ImageInput, you can now specify an image modifier to be applied to the loaded image. Based on the intervention/image package.
* Changed artisan commands for creating new controllers, now when specifying a model, the controller fields will be automatically created from the list of model fields, namely from `fillable`.
* Changed lazy loading for Chart.js, now charts can be loaded after the page is loaded.
* Changed the core is responsible for working with languages, now links are not duplicated.
* Changed Vue support.
* Fixed translations for the interface.
* Fixed live and watch zones hash generation.
* Fixed backend validation in forms.
* Fixed dashboard widgets design.
* Fixed IDE helper.
* Fixed NProgress position.
* Fixed save search request.
* Fixed model table formatter `to_json`.
* Fixed support Select2 in modal.
* Fixed crypt fields.
