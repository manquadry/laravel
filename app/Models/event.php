<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class event extends Model
{
    use HasFactory;


    protected $table ='event';

    protected $fillable = [
        'EventId',
        'Title',
        'Description',
        'startdate',
        'enddate',
        'Time',
        'Moderator',
        'Minister',
        'Type',
        'parishcode',
        'parishname',
        'eventImg',
       ];









}
