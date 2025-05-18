<?php
namespace Album\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class AlbumTable 
{
    private $tableGatway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGatway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGatway->select();
    }

    public function getAlbum($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGatway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }
        return $row;
    }

    public function saveAlbum(Album $album)
    {
        $data = [
            'artist' => $album->artist,
            'title' => $album->title,
        ];

        $id = (int) $album->id;

        if ($id === 0) {
            $this->tableGatway->insert($data);
            return;
        }

        try {
            $this->getAlbum($id);
        }
        catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update album with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGatway->update($data, ['id' => $id]);
    }

    public function deleteAlbum($id)
    {
        $this->tableGatway->delete(['id' => (int) $id]);
    }
}