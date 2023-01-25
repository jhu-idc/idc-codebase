<?php

namespace Drupal\Tests\islandora\Kernel;

use Drupal\islandora\Flysystem\Fedora;
use League\Flysystem\AdapterInterface;
use Islandora\Chullo\IFedoraApi;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;

/**
 * Tests the Fedora plugin for Flysystem.
 *
 * @group islandora
 * @coversDefaultClass \Drupal\islandora\Flysystem\Fedora
 */
class FedoraPluginTest extends IslandoraKernelTestBase {

  /**
   * Mocks up a plugin.
   */
  protected function createPlugin($return_code) {
    $prophecy = $this->prophesize(ResponseInterface::class);
    $prophecy->getStatusCode()->willReturn($return_code);
    $response = $prophecy->reveal();

    $prophecy = $this->prophesize(IFedoraApi::class);
    $prophecy->getResourceHeaders('')->willReturn($response);
    $prophecy->getBaseUri()->willReturn("");
    $api = $prophecy->reveal();

    $mime_guesser = $this->prophesize(MimeTypeGuesserInterface::class)->reveal();

    $language_manager = $this->container->get('language_manager');

    return new Fedora($api, $mime_guesser, $language_manager);
  }

  /**
   * Tests the getAdapter() method.
   *
   * @covers \Drupal\islandora\Flysystem\Fedora::getAdapter
   */
  public function testGetAdapter() {
    $plugin = $this->createPlugin(200);
    $adapter = $plugin->getAdapter();

    $this->assertTrue($adapter instanceof AdapterInterface, "getAdapter() must return an AdapterInterface");
  }

  /**
   * Tests the ensure() method.
   *
   * @covers \Drupal\islandora\Flysystem\Fedora::ensure
   */
  public function testEnsure() {
    $plugin = $this->createPlugin(200);
    $this->assertTrue(empty($plugin->ensure()), "ensure() must return an empty array on success");

    $plugin = $this->createPlugin(404);
    $this->assertTrue(!empty($plugin->ensure()), "ensure() must return a non-empty array on fail");
  }

}
