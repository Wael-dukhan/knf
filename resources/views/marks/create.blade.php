@extends('layouts.app2')

@section('title', __('messages.entry_form_title'))

@section('content')
<div class="page-wrapper">
    <div class="container-fluid mt-5 card shadow-sm p-4 mb-4">
        <style>
            .d-flex.column-gap-10 {
                column-gap: 10px;
            }
            a.btn.btn-secondary {
                height: fit-content;
                align-self: end;
            }
        </style>
        <h2 class="mb-4">{{ __('messages.marks.entry_form_title', ['material' => $material->name]) }}</h2>
        <div class="mb-3">
            <strong>{{ __('messages.school') }}:</strong> {{ $classSectionInfo->school_name }} - 
            <strong>{{ __('messages.grade') }}:</strong> {{ $classSectionInfo->grade_name }}<br>
            <strong>{{ __('messages.academic_year') }}:</strong> {{ $classSectionInfo->academic_year_name }} - 
            <strong>{{ __('messages.term') }}:</strong> {{ $termName }}
        </div>

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
            {{-- <div class="mb-3">
                <label for="term_id" class="form-label">{{ __('messages.term') }}</label>
                <select name="term_id" id="term_id" class="form-select" required>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" {{ old('term_id', $selectedTermId ?? '') == $term->id ? 'selected' : '' }}>
                            {{ $term->name }}
                        </option>
                    @endforeach
                </select>
            </div> --}}

            <table id="marksTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('messages.marks.student_name') }}</th>
                        <th>{{ __('messages.marks.oral_mark') }}</th>
                        <th>{{ __('messages.marks.homework_mark') }}</th>
                        <th>{{ __('messages.marks.first_study') }}</th>
                        <th>{{ __('messages.marks.second_study') }}</th>
                        <th>{{ __('messages.marks.work_total') }}</th>
                        <th>{{ __('messages.marks.oral_exam') }}</th>
                        <th>{{ __('messages.marks.written_exam') }}</th>
                        <th>{{ __('messages.marks.first_term_total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][oral]" class="form-control mark-input"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('marks.'.$student->id.'.oral', $existingMarks[$student->id]->oral_mark ?? 0) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][homework]" class="form-control mark-input"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('marks.'.$student->id.'.homework', $existingMarks[$student->id]->homework_mark ?? 0) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][first_study]" class="form-control mark-input"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('marks.'.$student->id.'.first_study', $existingMarks[$student->id]->first_study_mark ?? 0) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][second_study]" class="form-control mark-input"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('marks.'.$student->id.'.second_study', $existingMarks[$student->id]->second_study_mark ?? 0) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number" class="form-control work-total" readonly>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][oral_exam]" class="form-control mark-input"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('marks.'.$student->id.'.oral_exam', $existingMarks[$student->id]->oral_exam_mark ?? 0) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number" name="marks[{{ $student->id }}][written_exam]" class="form-control mark-input"
                                   min="0" max="100" step="0.01"
                                   value="{{ old('marks.'.$student->id.'.written_exam', $existingMarks[$student->id]->written_exam_mark ?? 0) }}"
                                   required>
                        </td>
                        <td>
                            <input type="number" class="form-control first-term-total" readonly>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex column-gap-10">
                <a href="{{ route('material-assignments.show', [$material->id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> {{ __('messages.back') }}
                </a>
                <button type="submit" class="btn btn-primary mt-2">{{ __('messages.marks.submit_button') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    var table = $('#marksTable').DataTable({
        orderCellsTop: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 50,
    });

    function calculateTotals(row) {
        const oral = parseFloat($(row).find('input[name$="[oral]"]').val()) || 0;
        const homework = parseFloat($(row).find('input[name$="[homework]"]').val()) || 0;
        const firstStudy = parseFloat($(row).find('input[name$="[first_study]"]').val()) || 0;
        const secondStudy = parseFloat($(row).find('input[name$="[second_study]"]').val()) || 0;
        const oralExam = parseFloat($(row).find('input[name$="[oral_exam]"]').val()) || 0;
        const writtenExam = parseFloat($(row).find('input[name$="[written_exam]"]').val()) || 0;

        const workTotal = (oral + homework + firstStudy + secondStudy) / 4;
        const firstTermTotal = (workTotal + oralExam + writtenExam) / 3;

        $(row).find('.work-total').val(workTotal.toFixed(2));
        $(row).find('.first-term-total').val(firstTermTotal.toFixed(2));
    }

    $('#marksTable tbody').on('input', '.mark-input', function() {
        var row = $(this).closest('tr');
        calculateTotals(row);
    });

    $('#marksTable tbody tr').each(function() {
        calculateTotals(this);
    });
});
</script>
@endpush
