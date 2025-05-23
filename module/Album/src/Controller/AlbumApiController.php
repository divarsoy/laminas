<?php
namespace Album\Controller;

use Album\Model\AlbumRepositoryInterface;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response;
use Album\Model\Album;
use Album\Form\AlbumForm;
use JsonException;

class AlbumApiController extends AbstractRestfulController 
{

    private $repository;
    public function __construct(AlbumRepositoryInterface $albumrepository)
    {
        $this->repository = $albumrepository;
    }
    public function getList() 
    {
        $rowset = $this->repository->getAlbums();
        $albums = $rowset->toArray();
        $data = [
            'message' => 'List of albums',
            'data' => $albums
        ];
        return new JsonModel($data);
    }

    public function get($id)
    {
        $rowset = $this->repository->getAlbum((int) $id);
        $data = [
            'message' => 'Single album',
            'data' => [
                'id' => $id,
                'title' => 'Album ' . $id
            ]
        ];
        
        return new JsonModel($data);
    }

    public function create($data)
    {
        $response = new Response();

        $form = new AlbumForm();
        $album = new Album();
        $form->setInputFilter($album->getInputFilter());
        $data['id']= 0; 

        $form->setData($data);
        if (!$form->isValid()) {
            $response = new Response();
            $response->setStatusCode(Response::STATUS_CODE_400);
            try {
                $json = json_encode(['error' => $form->getMessages()], JSON_THROW_ON_ERROR);
            } catch(JsonException $e) {
                error_log('JSON encode error: ' . $e->getMessage());
                $json = '{}';
            }
            $response->setContent($json);
            return $response;
        }

        $album->exchangeArray($form->getData());
        $createdAlbum = $this->repository->createAlbum($album);
        
        return new JsonModel([
            'message' => 'Album created successfully',
            'data' => $createdAlbum
        ]);
    }
    
    public function update($id, $data)
    {
        if(empty($id)){
            return new JsonModel([
                'message' => 'no id'
            ]);
        }
        return new JsonModel([
            'message' => 'Album updated',
            'id' => $id,
            'data' => $data
        ]);
    }

    public function delete($id) 
    {
        return new JsonModel([
            'message' => 'Album deleted',
            'id' => $id
        ]);
    }
}