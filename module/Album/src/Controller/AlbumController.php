<?php
namespace Album\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Model\AlbumTable;
use Album\Form\AlbumForm;
Use Album\Model\Album;
use JsonException;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response;

Class AlbumController extends AbstractActionController
{
    private $table;

    public function __construct(AlbumTable $table)
    {
        $this->table = $table;
    }
    public function indexAction()
    {
        return new ViewModel([
            'albums' => $this->table->fetchAll(),
        ]);
    }

    public function apiAction()
    {
        $albums = $this->table->fetchAll();
        $data = [];

        foreach ($albums as $album) {
            $data[] = [
                'id' => $album->id,
                'title' => $album->title,
                'artist' => $album->artist,
            ];
        }
        return new JsonModel($data);
    }

    public function apiAddAction()
    {  
        $request = $this->getRequest();

        $response = new Response();
        if ( ! $request->isPost()) {
            $response->setStatusCode(Response::STATUS_CODE_403);
            return $response;
        }

        $form = new AlbumForm();
        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $content = json_decode($request->getContent(),true);

        $form->setData($content);
        if( ! $form->isValid()) {
            $response->setStatusCode(Response::STATUS_CODE_400);
            try {
                $json = json_encode(['error'=> $form->getMessages()], JSON_THROW_ON_ERROR);
            } catch(JsonException $e){
                error_log('JSON encode error: ' . $e->getMessage());
                $json = '{}'; 
            }
            $response->setContent($json);
            return $response;
        }

        $album->exchangeArray($form->getData());
        $this->table->saveAlbum($album);
        $response->isOk();
        $response->setContent(json_encode($album), JSON_THROW_ON_ERROR);
        return $response;

    }

    public function apiDeleteAction() {

        $response = new Response();
        $id = (int) $this->params()->fromRoute('id',0);
        if( ! $id) {
            $response->setStatusCode(Response::STATUS_CODE_400);
            $response->setContent(json_encode(['error' => 'No ID defined in url path']));
            return $response;
        }

        $request = $this->getRequest();

        if ($request->isDelete()) {
            $this->table->deleteAlbum((int) $id);
            $response->isOk();
            $response->setContent(json_encode(['data' => 'ok']), JSON_THROW_ON_ERROR);
            return $response;
        }
        
        $response->setStatusCode(Response::STATUS_CODE_403);
        return $response;

    }

    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');
        $request = $this->getRequest();
    
        if ( ! $request->isPost()) {
            return ['form' => $form];
        }

        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if( ! $form->isValid()) {
            return ['form' => $form];
        }

        $album->exchangeArray($form->getData());
        $this->table->saveAlbum($album);
        return $this->redirect()->toRoute('album');

    }

    public function editAction() {
        $id = (int) $this->params()->fromRoute('id',0);
        
        if ( 0 === $id) {
            return $this->redirect()->toRoute('album', ['action' => 'add']);
        }

        try {
            $album = $this->table->getAlbum($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('album', ['action' => 'index']);
        }

        $form = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        $form->setInputFilter($album->getInputFilter());
        $form->setData($request->getPost());

        if( ! $form->isValid()) {
            return $viewData;
        }

        try {
            $this->table->saveAlbum($album);
        } catch(\Exception $e) {

        }

        return $this->redirect()->toRoute('album', ['action' => 'index']);

    }

    public function deleteAction() {
        $id = (int) $this->params()->fromRoute('id',0);
        if( ! $id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteAlbum($id);
            }

            return $this->redirect()->toRoute('album');
        }

        return [
            'id' => $id,
            'album' => $this->table->getAlbum($id),
        ];
    }
}