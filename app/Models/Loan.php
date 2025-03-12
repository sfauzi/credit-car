<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_name',
        'car_price',
        'down_payment',
        'installment_months',
        'monthly_installment',
    ];
}
