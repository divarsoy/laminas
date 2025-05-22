<?php
namespace Album\Controller;

use Album\Model\AlbumRepositoryInterface;
use Laminas\Mvc\Controller\AbstractRestfulController;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response;

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
        $data = [
            'message' => 'Single album',
            'data' => [
                'id' => $id,
                'title' => 'Album ' . $id
            ]
        ];
        
        return new JsonModel($data);
    }

    public function update($id, $data)
    {
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