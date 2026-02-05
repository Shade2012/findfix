<?php
namespace App\Interfaces;

interface FoundRepositoryInterface{
    public function getNewestFound();

    public function getCountReport();

    public function getFounds(array $params = []);
    public function getFoundStatus();
    public function getFoundCategory();

    public function createReport(array $params = []);   
    public function createFoundImages(array $data = []);
}
