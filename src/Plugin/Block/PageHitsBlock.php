<?php

/**
 * @file
 * Contains \Drupal\page_hits\Plugin\Block\PageHitsBlock.
 */
 
namespace Drupal\page_hits\Plugin\Block;
 
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;  
 
 
/**
 * Provides a 'page_hits' block.
 *
 * @Block(
 *   id = "page_hits_block",
 *   admin_label = @Translation("Page Hits"),
 *   category = @Translation("Page Hits block")
 * )
 */
class PageHitsBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = \Drupal::config('page_hits.settings');
    $ip = \Drupal::request()->getClientIp();
    $unique_visitor = 0;
    $total_visitor = 0;
    $total_visitor_by_user = 0;
    $total_visitor_in_week = 0;
    $current_user = \Drupal::currentUser();

    global $base_url;
    $page_url =  $base_url . \Drupal::request()->getRequestUri();

    $result = [];
    $node =  \Drupal::request()->attributes->get('node');
    if(!empty($node))
    {
      $result =  page_hits_get_data_by_nid($node->id());
    }
    else {
      $result =  page_hits_get_data_by_url($page_url);
    }

    if(!empty($result)) 
    {
      $ip = $result['ip'];
      $unique_visitor = $result['unique_visits'];
      $total_visitor = $result['total_visitors'];
      $total_visitor_by_user = $result['total_visitor_by_user'];
      $total_visitor_in_week = $result['total_visitor_in_week'];
    }

    $output = '<div id="counter">';
    $output .= '<ul>';

    if($config->get('show_user_ip_address')){
      $output .= '<li>'.$this->t('YOUR IP: ').'<strong>'.$ip.'</strong></li>';
    }
    if($config->get('show_unique_page_visits')){
      $output .= '<li>'.$this->t('UNIQUE VISITORS: ').'<strong>'.number_format($unique_visitor).'</strong></li>';
    }
    if($config->get('show_total_page_count')){
      $output .= '<li>'.$this->t('TOTAL VISITORS: ').'<strong>'.number_format($total_visitor).'</strong></li>';
    }
    if($config->get('show_page_count_of_logged_in_user') &&  !empty($current_user) && !empty($current_user->id())){
      $output .= '<li>'.$this->t('TOTAL VISITS BY YOU: ').'<strong>'.number_format($total_visitor_by_user).'</strong></li>';
    }
    if($config->get('show_total_page_count_of_week')){
      $output .= '<li>'.$this->t('TOTAL VISITS IN THIS WEEK: ').'<strong>'.number_format($total_visitor_in_week).'</strong></li>';
    }
     
    $output .= '</ul>';
    $build['#markup'] = $output;
    $build['#cache']['max-age'] = 0;
    $build['#allowed_tags'] = [
      'div', 'script', 'style', 'link', 'form',
      'h2', 'h1', 'h3', 'h4', 'h5',
      'table', 'thead', 'tr', 'td', 'tbody', 'tfoot',
      'img', 'a', 'span', 'option', 'select', 'input',
      'ul', 'li', 'br', 'p', 'link', 'hr', 'style', 'img',
 
    ];
    return $build;
  }
}