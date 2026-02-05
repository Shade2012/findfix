<?php

namespace App\Utils;
enum Status: int {
    case DITEMUKAN = 1;
    case HILANG = 2;
    case DIKEMBALIKAN = 3;
    case TERSIMPAN = 4;
}