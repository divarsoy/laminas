<?php
namespace AlbumTest\Controller;

use Album\Controller\AlbumController;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Album\Model\AlbumTable;
use Album\Model\Album;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Http\Response;

class AlbumControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = true;
    protected $albumTable;

    protected function setUp() : void
    {
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));
        parent::setup();
        $this->configureServiceManager($this->getApplicationServiceLocator());
    }

    protected function configureServiceManager(ServiceManager $services): void
    {
        $services->setAllowOverride(true);
        $services->setService('config', $this->updateConfig($services->get('config')));
        $services->setService(AlbumTable::class, $this->mockAlbumTable());
        $services->setAllowOverride(false);

    }

    protected function updateConfig($config)
    {
        $config['db'] = [];
        return $config;
    }

    protected function mockAlbumTable(): AlbumTable
    {
        $this->albumTable = $this->createMock(AlbumTable::class);
        return $this->albumTable;
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->albumTable->expects($this->once())
            ->method('fetchall')
            ->willReturn([]);
        $this->dispatch('/album');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Album');
        $this->assertControllerName(AlbumController::class);
        $this->assertControllerClass('AlbumController');
        $this->assertMatchedRouteName('album');
    }

    public function testAddActionRedirectAfterValidPost()
    {
        $this->albumTable->expects($this->once())
            ->method('saveAlbum')
            ->with($this->isInstanceOf(Album::class));
    
    $postData = [
        'title' => 'Lez Zeppelin III',
        'artist' => 'Led Zeppeling',
        'id' => '',
    ];

    $this->dispatch('/album/add', 'POST', $postData);
    $this->assertResponseStatuscode(302);
    $this->assertRedirectTo('/album');
    }       

    public function testApiDeleteEndpointCanBeAccessedViaDELETE()
    {
        $id = 1;
        $this->albumTable->expects($this->once())
            ->method('deleteAlbum')
            ->with($id);
        $this->dispatch('/album/apiDelete/'.$id, 'DELETE');
        $this->assertResponseStatusCode(Response::STATUS_CODE_200);
        
        $response = $this->getResponse()->getContent();
        $this->assertSame(json_encode(['data' => 'ok']), $response);
    }   

    public function testApiDeleteEndpointCanNotBeAccessedViaGET ()
    {
        $id = 1;
        $this->dispatch('/album/apiDelete/'.$id, 'GET');
        $this->assertResponseStatusCode(Response::STATUS_CODE_403);
        // $this->assertContains(json_encode(['data' => 'ok'], JSON_THROW_ON_ERROR), );
    }   
}