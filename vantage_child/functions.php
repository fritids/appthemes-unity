<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package Vantage
 * @author AppThemes
 */

/* current_category_ID()
 *
 * Get the currently-being-viewed category's ID.
 *
 * THE PROBLEM:
 * WordPress offers no easy way to get the id of the category that is currently
 * being viewd in an index.php. It offers nice conventions to get the title and
 * description (within the loop), but I needed this information outside the loop.
 *
 * THE SOLUTION:
 * After searching WordPress' documentation, get_queried_object() finally emerged
 * as a way to functionally get this information.
 */
function current_category_ID()
{
  $category = get_queried_object();
  return $category->term_id;
}