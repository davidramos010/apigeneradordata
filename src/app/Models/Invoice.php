<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'invoice_number', 'invoice_date', 'due_date', 'total', 'status'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
