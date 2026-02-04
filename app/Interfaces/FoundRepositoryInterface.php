<?php
namespace App\Interfaces;

use App\Models\Found;
use App\Models\FoundStatus;
use App\Models\FoundCategory;
use App\Models\Room;
interface FoundRepositoryInterface{
    public function getNewestFound();

    public function getCountReport();

    public function getFounds(array $params = []);
    
}
