<?php
namespace Album\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Album\Model\AlbumTable;
use Album\Form\AlbumForm;
Use Album\Model\Album;

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
        
    }
}