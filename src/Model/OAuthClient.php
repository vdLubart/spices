<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class OAuthClient extends Model
{
    protected $table = 'oauth2_client';

    protected $fillable = ['name', 'secret'];
}