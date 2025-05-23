<?php
namespace Album\Model;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Laminas\Db\Sql\Sql;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Hydrator\HydratorInterface;
use RuntimeException;

class AlbumRepository implements AlbumRepositoryInterface 
{

    private $db;
    private $hydrator;
    private $album;

    const ALBUM = 'album';
    public function __construct(
        AdapterInterface $db,
        HydratorInterface $hydrator,
        Album $albumPrototype)
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->album = $albumPrototype;

    }
    public function getAlbums(): HydratingResultSet
    {
        $sql = new Sql($this->db);
        $select = $sql->select()
            ->columns(['id', 'title', 'artist'])
            ->from(AlbumRepository::ALBUM);
        $statement   = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        if( ! $result instanceOf ResultInterface || !$result->isQueryResult()){
            throw new RuntimeException('Failed retrieving albums from the database');
        }

        $resultSet = new HydratingResultSet($this->hydrator, $this->album);
        $resultSet->initialize($result);
        return $resultSet;
    }

    public function getAlbum(int $id):Album
    {
        return new Album;
    }
    public function createAlbum(Album $album)    
    {
        $sql = new Sql($this->db);
        $insert = $sql->insert(AlbumRepository::ALBUM);
        $insert->values([
            'artist' => $album->artist,
            'title' => $album->title
        ]);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result = $statement->execute();
        $album->id = $result->getGeneratedValue();
        return $album;    
    }

    public function updateAlbum(Album $album)    
    {
        
    }
    
    public function deleteAlbum(int $id)
    {
        
    }

}
