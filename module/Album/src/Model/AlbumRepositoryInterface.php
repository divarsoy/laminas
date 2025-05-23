<?php
namespace Album\Model;
use Laminas\Db\ResultSet\HydratingResultSet;

interface AlbumRepositoryInterface 
{

    public function getAlbums(): HydratingResultSet;

    public function getAlbum(int $id): Album;

    public function createAlbum(Album $album);

    public function updateAlbum(Album $album);

    public function deleteAlbum(int $id);

}
