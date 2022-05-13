<?php

namespace Drupal\multi_sitemap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\File\FileSystem;
use Symfony\Component\DependencyInjection\ContainerInterface;
use samdark\sitemap\Sitemap;
use samdark\sitemap\Index;
use Symfony\Component\HttpFoundation\Response;

/**
 * Returns responses for Multi sitemap routes.
 */
class MultiSitemapController extends ControllerBase {
  protected $FileSystem;
  
  function __construct(FileSystem $FileSystem) {
    $this->FileSystem = $FileSystem;
  }
  
  /**
   *
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *        The Drupal service container.
   *        
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('file_system'));
  }
  
  /**
   * Builds the response.
   */
  public function build() {
    $uri = 'public://multi_sitemap/content_generate_entity/estimation_de_devis/sitemap.xml';
    $path = $this->FileSystem->realpath($uri);
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent(file_get_contents($path));
    return $response;
  }
  
  public function RenovBuild() {
    $uri = 'public://multi_sitemap/content_generate_entity/renovation_maison/sitemap.xml';
    $path = $this->FileSystem->realpath($uri);
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent(file_get_contents($path));
    return $response;
  }
  
  public function RenovAppart() {
    $uri = 'public://multi_sitemap/content_generate_entity/renovation_appartement/sitemap.xml';
    $path = $this->FileSystem->realpath($uri);
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent(file_get_contents($path));
    return $response;
  }
  
  public function IsolaExterieur() {
    $uri = 'public://multi_sitemap/content_generate_entity/isolation_exterieure/sitemap.xml';
    $path = $this->FileSystem->realpath($uri);
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent(file_get_contents($path));
    return $response;
  }
  
  public function SiteMap93() {
    $uri = 'public://multi_sitemap/content_generate_entity/custom/sitemap-93.xml';
    $path = $this->FileSystem->realpath($uri);
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent(file_get_contents($path));
    return $response;
  }
  
  public function SiteMap92() {
    $uri = 'public://multi_sitemap/content_generate_entity/custom/sitemap-92.xml';
    $path = $this->FileSystem->realpath($uri);
    $response = new Response();
    $response->headers->set('Content-Type', 'text/xml');
    $response->setContent(file_get_contents($path));
    return $response;
  }
  
  /**
   *
   * @return string[]|\Drupal\Core\StringTranslation\TranslatableMarkup[]
   */
  public function cron($entity_type_id, $bundle = null) {
    $domaine = 'https://lesroisdelareno.fr';
    $Contents = [];
    $directory = 'public://multi_sitemap/' . $entity_type_id;
    /**
     *
     * @var \Drupal\Core\Entity\EntityStorageInterface $entity_type
     */
    $entityStorage = $this->entityTypeManager()->getStorage($entity_type_id);
    /**
     *
     * @var \Drupal\Core\Entity\EntityTypeInterface $entity_type
     */
    $entity_type = $entityStorage->getEntityType();
    // dump($entity_type);
    $values = [ // 'id' => [
                 // 2,
                 // 3,
                 // 4,
                 // 5,
                 // 6,
                 // 7,
                 // 8,
                 // 9,
                 // 10,
                 // 11,
                 // 12,
                 // 13
                 // ]
    ];
    if ($bundle && $entity_type->hasKey('bundle')) {
      if ($entity_type->getBundleEntityType()) {
        $directory .= '/' . $bundle;
        $values[$entity_type->getKey('bundle')] = $bundle;
      }
    }
    if (!file_exists($directory)) {
      $this->FileSystem->mkdir($directory, null, true);
    }
    
    $Contents = $entityStorage->loadByProperties($values);
    $full_directory = $this->FileSystem->realpath($directory);
    // create sitemap
    $sitemap = new Sitemap($full_directory . '/sitemap.xml');
    
    // add some URLs
    foreach ($Contents as $content) {
      /**
       *
       * @var \Drupal\generate_mapping_content\Entity\ContentGenerateEntity $entity
       */
      $entity = $content;
      $sitemap->addItem($domaine . $entity->toUrl()->toString(), $entity->getChangedTime(), Sitemap::MONTHLY);
    }
    
    // write it
    $sitemap->write();
    //
    $build['content'] = [
      '#type' => 'item',
      '#markup' => ' OK '
    ];
    return $build;
  }
  
}
