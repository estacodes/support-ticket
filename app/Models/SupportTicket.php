<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = ['name','email','department','related_service','priority','status','subject','message',
        'attachment','assignee','ticket_id'];

    protected $hidden = [
        'id'
    ];

    public function user() {
        return $this->belongsTo('App\Models\User','assignee','id');
    }
}
