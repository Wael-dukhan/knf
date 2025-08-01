@extends('layouts.app2')

@section('title', __('messages.entry_form_title'))

@section('content')
<div class="page-wrapper">
    <div class="container mt-5 card shadow-sm p-4 mb-4">
        <h2 class="mb-4">{{ __('messages.marks.entry_form_title', ['material' => $material->name]) }}</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ __('messages.marks.success_message') }}</div>
        @endif

        <form action="{{ route('marks.store') }}" method="POST">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <input type="hidden" name="material_id" value="{{ $material->id }}">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.marks.student_name') }}</th>
                        <th>{{ __('messages.marks.oral_mark') }}</th>
                        <th>{{ __('messages.marks.homework_mark') }}</th>
                        <th>{{ __('messages.marks.study_mark') }}</th>
                        <th>{{ __('messages.marks.work_total') }}</th>
                        <th>{{ __('messages.marks.first_term_exam') }}</th>
                        <th>{{ __('messages.marks.first_term_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][oral]" class="form-control mark-input" min="0" max="100" step="0.01" value="0" required>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][homework]" class="form-control mark-input" min="0" max="100" step="0.01" value="0" required>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][study]" class="form-control mark-input" min="0" max="100" step="0.01" value="0" required>
                        </td>
                        <td>
                            <input type="number" class="form-control work-total" readonly>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][first_term_exam]" class="form-control mark-input" min="0" max="100" step="0.01" value="0" required>
                        </td>
                        <td>
                            <input type="number" class="form-control first-term-total" readonly>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-primary">{{ __('messages.marks.submit_button') }}</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function calculateTotals(row) {
        const oral = parseFloat(row.querySelector('input[name$="[oral]"]').value) || 0;
        const homework = parseFloat(row.querySelector('input[name$="[homework]"]').value) || 0;
        const study = parseFloat(row.querySelector('input[name$="[study]"]').value) || 0;
        const firstTermExam = parseFloat(row.querySelector('input[name$="[first_term_exam]"]').value) || 0;

        const workTotal = (oral + homework + study)/3;
        const firstTermTotal = (workTotal + firstTermExam)/2;

        row.querySelector('.work-total').value = workTotal.toFixed(2);
        row.querySelector('.first-term-total').value = firstTermTotal.toFixed(2);
    }

    document.querySelectorAll('tbody tr').forEach(row => {
        row.querySelectorAll('.mark-input').forEach(input => {
            input.addEventListener('input', () => calculateTotals(row));
        });
        calculateTotals(row);
    });
});
</script>
@endsection
