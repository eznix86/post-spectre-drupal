<?php

namespace Drupal\post_spectre\EventSubscriber;

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
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   Response event.
   */
  public function onKernelResponse(FilterResponseEvent $event) {
    $response = $event->getResponse();
    $request = $event->getRequest();
    $response = $this->addDefaultPostSpectreHeaders($response);

    $allow = $this->allowRequestWithFetchMetadata($request);

    if (!$allow) {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_FORBIDDEN);
    }

    $event->setResponse($response);
  }

  public function addDefaultPostSpectreHeaders(Response $response) {
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
    //TODO: Exempt paths/endpoints meant to be served cross-origin.

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
