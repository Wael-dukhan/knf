@extends('layouts.table-layout2')

@section('title', __('messages.users_list'))

@section('content')
<div class="">
    <div class="">
        <!-- Page Header -->
        <div class="mb-2">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">{{ __('messages.users_list') }}</h3>
                </div>
                <div class="col-auto text-end float-end ms-auto">
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('messages.add_user') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Users Table -->
        <div class="card card-table">
            <div class="card-body">
                <table class="table table-bordered table-hover table-center mb-0" id="usersTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.name') }} </th>
                            <th>{{ __('messages.email') }}</th>
                            <th>
                                {{ __('messages.role') }}
                            </th>
                            <th>
                                {{ __('messages.school') }}                                
                            </th>
                            <th> {{ __('messages.gender') }}
                            <th>{{ __('messages.created_at') }}</th>
                            <th class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <input type="text" id="nameSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th>
                                <input type="text" id="emailSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th>
                                <select id="roleSearch" class="form-select form-select-sm">
                                    <option value="cancel">{{ __('messages.choose') }}</option> <!-- خيار إلغاء التحديد -->
                                    @foreach($roles as $role)
                                        <option value="{{ __("messages." .$role->name ) }}">{{ __("messages." . $role->name) }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th>
                                <select id="schoolSearch" class="form-select form-select-sm">
                                    <option value="cancel">{{ __('messages.choose') }}</option> <!-- خيار إلغاء التحديد -->
                                    @foreach($schools as $school)
                                        <option value="{{ $school->name }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th></th>
                            <th>
                                 <input type="text" id="createdAtSearch" class="form-control form-control-sm" placeholder="{{ __('messages.search') }}">
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td data-role="{{ $user->getRoleNames()->first() }}">{{ $user->getRoleNames()->first() ? __("messages." . $user->getRoleNames()->first() ) : __('messages.not_defined') }}</td>
                                <td>{{ $user->school->name ?? __('messages.not_defined') }}</td> <!-- عرض اسم المدرسة -->
                                <td>{{ $user->gender ? __("messages.".$user->gender) : __('messages.not_defined') }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td class="text-end">
                                    <div class="actions">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm bg-info-light me-2">
                                            <i class="feather-eye"></i> {{ __('messages.show') }}
                                        </a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm bg-warning-light me-2">
                                            <i class="feather-edit"></i> {{ __('messages.edit') }}
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm bg-danger-light" onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                                <i class="feather-trash-2"></i> {{ __('messages.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('messages.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        let table = $('#usersTable').DataTable({
            dom: 'Bfrtip',
            order: [[0, 'asc']],
            orderCellsTop: true,
            fixedHeader: true,
            buttons: [
                {
                    extend: 'excelHtml5',
                    text: '{{ __("messages.export_excel") }}',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '{{ __("messages.export_pdf") }}',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible:not(:last-child)'
                    },
                    customize: function (doc) {
                        doc.defaultStyle.alignment = 'right';
                        doc.styles.tableHeader.alignment = 'center';
                    }
                },
                {
                    extend: 'print',
                    text: '{{ __("messages.print") }}',
                    customize: function (win) {
                        $(win.document.body)
                            .css('direction', 'rtl')
                            .css('text-align', 'right')
                            .find('table')
                            .addClass('table table-bordered');

                        $(win.document.body).find('table th:last-child, table td:last-child').css('display', 'none');
                    }
                }
            ],
            initComplete: function () {
                $('#schoolSearch').select2({
                    width: '100%',
                    placeholder: '{{ __("messages.select_school") }}',
                    allowClear: true
                });

                $('#roleSearch').select2({
                    width: '100%',
                    placeholder: "{{ __('messages.select_role') }}",
                    allowClear: true
                });
            },
            language: {
                search: "{{ __('messages.search') }}",
                lengthMenu: "{{ __('messages.show') }} _MENU_",
                info: "{{ __('messages.showing') }} _START_ {{ __('messages.to') }} _END_ {{ __('messages.of') }} _TOTAL_",
                paginate: {
                    previous: "{{ __('messages.previous') }}",
                    next: "{{ __('messages.next') }}"
                }
            }
        });

            // فلترة حسب الاسم والبريد والتاريخ
        $('#nameSearch').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });

        $('#emailSearch').on('keyup', function () {
            table.column(2).search(this.value).draw();
        });

        $('#createdAtSearch').on('keyup', function () {
            table.column(5).search(this.value).draw();
        });

        // فلترة حسب المدرسة
        $('#schoolSearch').on('change', function () {
            let val = $(this).val();
            if (val === 'cancel') { // إذا كان الخيار هو إلغاء التحديد
                val = '';
            } // اجعل القيمة فارغة
            table.column(4).search(val).draw(); // تأكد أن العمود رقم 4 هو للمدرسة
        });
        // فلترة حسب الدور
        $('#roleSearch').on('change', function () {
                let role = $(this).val();
                if (role && role !== 'cancel') { // إذا كان هناك اختيار للدور
                    // نستخدم الترجمة كما تظهر في الجدول
                    let translated = '{{ __("__role__") }}'.replace('__role__', role);
                    console.log(translated);
                    table.column(3).search(translated).draw();
                } else {
                    table.column(3).search('').draw();
                }
            });
    });
</script>
@endpush
