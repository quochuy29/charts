<?php

namespace App\Repositories;

use App\Models\Node;

class NodeRepository extends BaseRepository
{
    public function __construct(Node $model)
    {
        parent::__construct($model);
    }

    public function getTreeStructure()
    {
        return $this->model->whereNull('parent_id')
            ->with(['children.children.children']) 
            ->get();
    }
}