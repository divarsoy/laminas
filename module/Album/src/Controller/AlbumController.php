<?php
namespace Album\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Model\AlbumTable;

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

    public function addAction()
    {

    }

    public function editAction() {
        
    }

    public function deleteAction() {
        
    }
}