<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (function_exists('pdc_add_options_page')) {
    pdc_add_options_page(array(
      'page_title' => 'Layout Setting',
      'menu_title' => 'Layouts',
      'menu_slug'  => 'layout-setting',
      'icon_url'   => 'dashicons-layout',
      'redirect'   => true
    ));

    pdc_add_options_sub_page(array(
      'page_title'  => 'General Layout',
      'menu_title'  => 'General',
      'parent_slug' => 'layout-setting',
    ));

    pdc_add_options_sub_page(array(
      'page_title'  => 'Header Layout',
      'menu_title'  => 'Header',
      'parent_slug' => 'layout-setting',
    ));
    
    pdc_add_options_sub_page(array(
      'page_title'  => 'Footer Layout',
      'menu_title'  => 'Footer',
      'parent_slug' => 'layout-setting',
    ));
}