<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    protected $fillable = [
        'lab_order_id', 'lab_order_test_id', 'file_path', 'original_filename',
        'mime_type', 'file_size', 'extracted_text', 'summary', 'result_data',
        'uploaded_by_lab_user_id', 'uploaded_by_user_id',
        'vet_approved', 'vet_approved_at', 'vet_notes',
        'retest_requested', 'retest_reason', 'visible_to_client',
    ];

    protected $casts = [
        'result_data' => 'array',
        'vet_approved' => 'boolean',
        'vet_approved_at' => 'datetime',
        'retest_requested' => 'boolean',
        'visible_to_client' => 'boolean',
    ];

    public function labOrder()
    {
        return $this->belongsTo(LabOrder::class);
    }

    public function test()
    {
        return $this->belongsTo(LabOrderTest::class, 'lab_order_test_id');
    }

    public function uploadedByLabUser()
    {
        return $this->belongsTo(LabUser::class, 'uploaded_by_lab_user_id');
    }

    public function uploadedByUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
