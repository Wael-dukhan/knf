<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class StudentController extends Controller
{
    public function updateStatus(Request $request, User $student)
    {
        // التحقق من صحة البيانات القادمة
        $request->validate([
            'status' => 'required|in:active,dropout,expelled,transferred_to_another_school,temporarily_suspended',
            'class_section_id' => 'required|exists:class_sections,id',
        ]);
        // dd($student->id, $request->status, $request->class_section_id);
        // تحقق من وجود السطر مسبقًا في جدول pivot
          // التحقق من وجود السطر حتى لو محذوف soft delete
        DB::table('student_class_section')
            ->where('user_id', $student->id)
            ->where('class_section_id', $request->class_section_id)
            ->update([
                'status' => $request->status,
                'deleted_at' => null,
                'updated_at' => now(),
            ]);

            
        return response()->json(['message' => 'Status updated successfully']);
    }
}
