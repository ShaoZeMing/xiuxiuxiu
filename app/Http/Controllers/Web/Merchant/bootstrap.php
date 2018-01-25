<?php

/**
 * Laravel-merchant - merchant builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Merchant.
 *
 * Here you can remove builtin form field:
 * ShaoZeMing\Merchant\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * ShaoZeMing\Merchant\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Merchant::css('/packages/prettydocs/css/styles.css');
 * Merchant::js('/packages/prettydocs/js/main.js');
 *
 */

ShaoZeMing\Merchant\Form::forget(['map', 'editor']);

//Merchant::js('https://cdn.bootcss.com/Chart.js/2.7.1/Chart.min.js');
app('view')->prependNamespace('merchant', resource_path('views/web/merchant'));