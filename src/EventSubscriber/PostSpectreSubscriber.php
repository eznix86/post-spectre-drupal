<?php

namespace Drupal\post_spectre\EventSubscriber;

use Drupal;
use Drupal\node\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * post_spectre event subscriber.
 */
class PostSpectreSubscriber implements EventSubscriberInterface {

  /**
   * Constructs event subscriber.
   */
  public function __construct() {}

    /**
     * Kernel response event handler.
     *
     * @param FilterResponseEvent $event
     *   Response event.
     * @throws Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
  public function onKernelResponse(FilterResponseEvent $event) {
    // Load the default configurations.
    $config = Drupal::config('post_spectre.settings');
    $post_spectre_custom_field = $config->get('post_spectre.custom_field_name');

    $response = $event->getResponse();
    $request = $event->getRequest();

    // Prevent pages like "edit", "revisions", etc from being redirected.
    $is_node = $request->attributes->get('_route') == 'entity.node.canonical';
    if (!$is_node) {
      return;
    }

    // Retrieve current node id.
    $current_node_id = (string) $request->attributes->get('node')->id();

    $node_storage = Drupal::entityTypeManager()->getStorage('node');
    /** @var Node $node */
    $node = $node_storage->load($current_node_id);

    if (!$node->hasField($post_spectre_custom_field)) {
      return;
    }

    $optOut = (bool) $node->get($post_spectre_custom_field)->opt_out;

    if ($optOut) {
        return;
    }

    $allow = $this->allowRequestWithFetchMetadata($request);

    if (!$allow) {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    $postSpectreType = $node->get($post_spectre_custom_field)->post_spectre_type;
    // based on this configuration Drupal\post_spectre\Constant\PostSpectreType::DEFAULT
    $response = $this->addDefaultPostSpectreHeaders($response);

    switch ($postSpectreType) {
        case Drupal\post_spectre\Constant\PostSpectreType::CROSS_ORIGIN_OPENERS_IFRAME:
            $response->headers->set('X-Frame-Options', 'ALLOWALL');
            break;
        case Drupal\post_spectre\Constant\PostSpectreType::CROSS_ORIGIN_OPENERS_POPUP:
            $response->headers->set('Cross-Origin-Opener-Policy', 'unsafe-none');
            $response->headers->set('X-Frame-Options', 'ALLOWALL');
            break;
        case Drupal\post_spectre\Constant\PostSpectreType::CROSS_ORIGIN_OPENERS:
            $response->headers->set('Cross-Origin-Opener-Policy', 'unsafe-none');
            break;
        case Drupal\post_spectre\Constant\PostSpectreType::OPEN_CROSS_ORIGIN_WINDOW:
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
            break;
        case Drupal\post_spectre\Constant\PostSpectreType::FULL_ISOLATION:
            $response->headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
            break;
        case Drupal\post_spectre\Constant\PostSpectreType::DEFAULT:
        default:
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
    }

    $event->setResponse($response);
  }

  public function addDefaultPostSpectreHeaders(Response $response): Response
  {
      // Add default headers for documents for post spectre
      $response->setVary('Sec-Fetch-Dest, Sec-Fetch-Mode, Sec-Fetch-Site, Sec-Fetch-User');
      $headers = [
          'Cross-Origin-Resource-Policy' => 'same-origin',
          'X-Content-Type-Options' => 'nosniff',
          'X-Frame-Options' => 'SAMEORIGIN'
      ];

      foreach ($headers as $key => $header) {
          $response->headers->set($key, $header);
      }

      return $response;
  }

    /**
     *
     * Resource Isolation Policy: Reject cross-origin requests to protect from CSRF, XSSI, and other bugs
     * @param Request $request
     * @return bool
     */
  public function allowRequestWithFetchMetadata(Request $request): bool {
    $headers = $request->headers->all();
    $headerKeys = array_keys($request->headers->all());

    // Allow requests from browsers which don't send Fetch Metadata
    if (in_array('sec-fetch-site', $headerKeys)) {
        return true;
    }
    // Allow same-site and browser-initiated requests
    if (in_array($headers['sec-fetch-site'], ['same-origin', 'same-site', 'none'])){
        return true;
    }
    // Allow simple top-level navigations except <object> and <embed>
    if ($headers['sec-fetch-mode'] === 'navigate' && $request->isMethod('GET') &&
        !in_array($headers['sec-fetch-dest'], ['object', 'embed'])) {
        return true;
    }
    // Reject all other requests that are cross-site and not navigational
    return false;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::RESPONSE => ['onKernelResponse'],
    ];
  }

}
