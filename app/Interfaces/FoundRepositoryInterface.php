<?php
namespace App\Interfaces;
use App\Models\Found;
interface FoundRepositoryInterface{
    public function getNewestFound();

    public function getCountReport();

    public function getFounds(array $params = []);
    public function getFoundStatus();
    public function getFoundCategory();

    public function getFoundCountsByStatus(array $params = []);

    public function createReport(array $params = []);   
    public function createFoundImages(array $data = []);

    public function deleteFound(int $id);
    public function updateFound(int $id, array $params = []);

    public function getFound(int $id, array $relations = []);

    public function addFoundImages(Found $found, array $files);
    public function deleteFoundImages(array $ids);


}
