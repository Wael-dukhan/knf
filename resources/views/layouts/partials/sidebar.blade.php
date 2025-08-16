<div class="sidebar" id="sidebar" style="display: block;">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="menu-title">
                    <span>{{__('messages.main_menu')}}</span>
                </li>
                {{-- <li class="submenu">
                    <a href="#"
                        ><i class="feather-grid"></i>
                        <span> Dashboard</span>
                        <span class="menu-arrow"></span
                    ></a>
                    <ul>
                        <li>
                            <a href="index.html">Admin Dashboard</a>
                        </li>
                        <li>
                            <a href="teacher-dashboard.html"
                                >Teacher Dashboard</a
                            >
                        </li>
                        <li>
                            <a href="student-dashboard.html"
                                >Student Dashboard</a
                            >
                        </li>
                    </ul>
                </li> --}}
                @if (Auth::user()->hasRole('super_admin'))
                    <li class="submenu {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-building"></i>
                            <span> {{ __('messages.schools') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('admin.schools.index') }}" class="{{ request()->routeIs('admin.schools.index') ? 'active' : '' }}">
                                    {{ __('messages.schools_list') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.schools.create') }}"
                                    class="{{ request()->routeIs('admin.schools.create') ? 'active' : '' }}"
                                    >{{ __('messages.create_school') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('admin.grades.create') }}"
                                    class="{{ request()->routeIs('admin.grades.create') ? 'active' : '' }}"
                                    >{{ __('messages.create_grade') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('admin.class_sections.create') }}"
                                    class="{{ request()->routeIs('admin.class_sections.create') ? 'active' : '' }}"
                                    >{{ __('messages.create_class_section') }}</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('admin.academic_years.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-clipboard"></i>
                            <span> {{ __('messages.the_academic_years') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('admin.academic_years.index') }}"
                                    class="{{ request()->routeIs('admin.academic_years.index') ? 'active' : '' }}"
                                    >{{ __('messages.academic_years_list') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('admin.academic_years.create') }}"
                                    class="{{ request()->routeIs('admin.academic_years.create') ? 'active' : '' }}"
                                    >{{ __('messages.add_academic_year') }}</a
                                >
                            </li>
                            
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('admin.terms.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-file-invoice-dollar"></i>
                            <span> {{ __('messages.terms') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('admin.terms.index') }}"
                                    class="{{ request()->routeIs('admin.terms.index') ? 'active' : '' }}"
                                    > {{ __('messages.terms_list') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('admin.terms.create') }}"
                                    class="{{ request()->routeIs('admin.terms.create') ? 'active' : '' }}"  >
                                    {{ __('messages.create_term') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('materials.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-chalkboard-teacher"></i>
                            <span> {{  __('messages.materials') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('materials.index') }}"
                                    class="{{ request()->routeIs('materials.index') ? 'active' : '' }}"
                                >{{ __('messages.materials_list') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('materials.create') }}"
                                    class="{{ request()->routeIs('materials.create') ? 'active' : '' }}"
                                    >{{ __('messages.create_material') }}</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-graduation-cap"></i>
                            <span> {{ __('messages.users') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('users.index') }}" 
                                class="{{ request()->routeIs('users.index') ? 'active' : '' }}"
                                >{{ __('messages.users_list') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('users.create') }}"
                                    class="{{ request()->routeIs('users.create') ? 'active' : '' }}"
                                    > {{ __('messages.create_user') }}</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('students.parents.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-book-reader"></i>
                            <span>{{ __('messages.parents') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('students.parents.index') }}"
                                class="{{ request()->routeIs('students.parents.index') ? 'active' : '' }}"
                                >{{ __('messages.parent_list') }}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('quran-levels.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fa fa-newspaper"></i>
                            <span> حلقات القرآن</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('quran-levels.index') }}"
                                class="{{ request()->routeIs('quran-levels.index') ? 'active' : '' }}"
                                >{{__('messages.all_quran_levels')}}</a></li>
                            <li>
                            <li><a href="{{ route('quran-classes.index') }}"
                                class="{{ request()->routeIs('quran-classes.index') ? 'active' : '' }}"
                                >{{__('messages.all_quran_classes')}}</a></li>
                            <li>
                                <a href="{{ route('quran-levels.create') }}"
                                class="{{ request()->routeIs('quran-levels.create') ? 'active' : '' }}"
                                >{{__('messages.create_quran_level')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('quran-classes.create') }}"
                                class="{{ request()->routeIs('quran-classes.create') ? 'active' : '' }}"
                                >{{__('messages.create_quran_class')}}</a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fa fa-chart-bar"></i>
                            <span>{{ __('messages.reports') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li>
                                <a href="{{ route('reports.student.marks_report') }}"
                                class="{{ request()->routeIs('reports.student.grades') ? 'active' : '' }}">
                                {{ __('messages.student_grades_report_in_materials') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.student.terms') }}"
                                class="{{ request()->routeIs('reports.student.terms') ? 'active' : '' }}">
                                {{ __('messages.student_terms_report') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.yearly_total_marks') }}"
                                class="{{ request()->routeIs('reports.yearly_total_marks') ? 'active' : '' }}">
                                {{ __('messages.student_annual_report') }}
                                </a>
                            </li>
                            {{-- <li>
                                <a href="{{ route('reports.class_ranking') }}"
                                class="{{ request()->routeIs('reports.class.rankings') ? 'active' : '' }}">
                                {{ __('messages.class_ranking_report') }}
                                </a>
                            </li> --}}
                            <li>
                                <a href="{{ route('reports.student.attendance') }}"
                                class="{{ request()->routeIs('reports.student.attendance') ? 'active' : '' }}">
                                {{ __('messages.attendance_report') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.overall_performance') }}"
                                class="{{ request()->routeIs('reports.overall.performance') ? 'active' : '' }}">
                                {{ __('messages.overall_performance_report') }}
                                </a>
                            </li>
                        </ul>
                    </li>

                @elseif (Auth::user()->hasRole('school_manager'))
                    <li class="submenu  {{ request()->routeIs('admin.schools.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-building"></i>
                            <span> {{ __('messages.school') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('grade_levels.index',Auth::user()->school_id) }}" class="{{ request()->routeIs('grade_levels.index') ? 'active' : '' }}">
                                    {{ __('messages.grade_levels') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.schools.show', Auth::user()->school_id) }}" class="{{ request()->routeIs('admin.schools.show') ? 'active' : '' }}">
                                    {{ __('messages.school_details') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('admin.schools.edit', Auth::user()->school_id) }}"
                                    class="{{ request()->routeIs('admin.schools.edit') ? 'active' : '' }}"
                                    >{{ __('messages.edit_school') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('teacher-attendance.index',Auth::user()->school_id) }}"
                                    class="{{ request()->routeIs('teacher-attendance.index') ? 'active' : '' }}"
                                    >{{ __('messages.teacher_attendance_log') }}</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('admin.academic_years.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-clipboard"></i>
                            <span> {{ __('messages.the_academic_years') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('admin.academic_years.index') }}"
                                    class="{{ request()->routeIs('admin.academic_years.index') ? 'active' : '' }}"
                                    >{{ __('messages.academic_years_list') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('admin.academic_years.create') }}"
                                    class="{{ request()->routeIs('admin.academic_years.create') ? 'active' : '' }}"
                                    >{{ __('messages.add_academic_year') }}</a
                                >
                            </li>
                            
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('admin.terms.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-file-invoice-dollar"></i>
                            <span> {{ __('messages.terms') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('admin.terms.index') }}"
                                    class="{{ request()->routeIs('admin.terms.index') ? 'active' : '' }}"
                                    > {{ __('messages.terms_list') }}</a
                                >
                            </li>
                            <li>
                                <a href="{{ route('admin.terms.create') }}"
                                    class="{{ request()->routeIs('admin.terms.create') ? 'active' : '' }}"  >
                                    {{ __('messages.create_term') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('materials.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-chalkboard-teacher"></i>
                            <span> {{  __('messages.materials') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('materials.index') }}"
                                    class="{{ request()->routeIs('materials.index') ? 'active' : '' }}"
                                >{{ __('messages.materials_list') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('materials.create') }}"
                                    class="{{ request()->routeIs('materials.create') ? 'active' : '' }}"
                                    >{{ __('messages.create_material') }}</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-graduation-cap"></i>
                            <span> {{ __('messages.users') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('users.index') }}" 
                                class="{{ request()->routeIs('users.index') ? 'active' : '' }}"
                                >{{ __('messages.users_list') }}</a>
                            </li>
                            <li>
                                <a href="{{ route('users.create') }}"
                                    class="{{ request()->routeIs('users.create') ? 'active' : '' }}"
                                    > {{ __('messages.create_user') }}</a
                                >
                            </li>
                        </ul>
                    </li>
                    <li class="submenu {{ request()->routeIs('students.parents.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fas fa-book-reader"></i>
                            <span>{{ __('messages.parents') }}</span>
                            <span class="menu-arrow"></span
                        ></a>
                        <ul>
                            <li>
                                <a href="{{ route('students.parents.index') }}"
                                class="{{ request()->routeIs('students.parents.index') ? 'active' : '' }}"
                                >{{ __('messages.parent_list') }}</a>
                            </li>
                    </li>
                @elseif (Auth::user()->hasRole('quran_supervisor'))
                    <li class="submenu  {{ request()->routeIs('quran-levels.*') || request()->routeIs('quran-classes.*') ? 'active' : '' }}">
                        <a href="#"
                            ><i class="fa fa-newspaper"></i>
                            <span> حلقات القرآن</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li><a href="{{ route('quran-levels.index') }}"
                                class="{{ request()->routeIs('quran-levels.index',Auth::user()->school_id) ? 'active' : '' }}"
                                >{{__('messages.all_quran_levels')}}</a></li>
                            <li>
                                <a href="{{ route('quran-classes.index') }}"
                                class="{{ request()->routeIs('quran-classes.index',Auth::user()->school_id) ? 'active' : '' }}"
                                >{{__('messages.all_quran_classes')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('quran-levels.create') }}"
                                class="{{ request()->routeIs('quran-levels.create') ? 'active' : '' }}"
                                >{{__('messages.create_quran_level')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('quran-classes.create') }}"
                                class="{{ request()->routeIs('quran-classes.create') ? 'active' : '' }}"
                                >{{__('messages.create_quran_class')}}</a>
                            </li>
                            <li>
                                <a href="{{ route('quran_teacher_attendance.index',Auth::user()->school_id) }}"
                                class="{{ request()->routeIs('quran_teacher_attendance.index') ? 'active' : '' }}"
                                >{{__('messages.quran_teacher_attendance_log')}}</a>
                            </li>

                        </ul>
                    </li>
                @elseif (Auth::user()->hasRole('teacher'))
                    <li class="submenu {{ request()->routeIs('teacher.grades.*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <span> {{ __('messages.my_classes') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li>
                                <a href="{{ route('teacher.grades.show') }}"
                                class="{{ request()->routeIs('teacher.grades.show') ? 'active' : '' }}">
                                    {{ __('messages.view_class_sections') }}
                                </a>
                            </li>
                        </ul>
                    </li>

                @elseif (Auth::user()->hasRole('quran_teacher'))
                    <li class="submenu {{ request()->routeIs('quran-teacher.*') ? 'active' : '' }}">
                        <a href="#">
                            <i class="fas fa-book-open"></i>
                            <span> {{ __('messages.quran_sessions') }}</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <ul>
                            <li>
                                <a href="{{ route('quran-teacher.myLevelsWithClasses') }}"
                                class="{{ request()->routeIs('quran-teacher.myLevelsWithClasses') ? 'active' : '' }}">
                                    {{ __('messages.my_quran_classes') }}
                                </a>
                            </li>
                        </ul>
                    </li>

                @elseif (Auth::user()->hasRole('parent'))
                    <li class="menu-item">
                        <a href="{{ route('profile.show') }}"
                        class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                            {{ __('messages.my_profile') }}
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('parent.children.index') }}"
                        class="{{ request()->routeIs(patterns: 'parent.children.index') ? 'active' : '' }}">
                            {{ __('messages.my_children') }}
                        </a>
                    </li>

                @elseif (Auth::user()->hasRole('student'))
                    <li class="menu-item">
                        <a href="{{ route('profile.show') }}"
                        class="{{ request()->routeIs('student.profile') ? 'active' : '' }}">
                            {{ __('messages.my_profile') }}
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('materials.index') }}"
                        class="{{ request()->routeIs('materials.index') ? 'active' : '' }}">
                            {{ __('messages.materials') }}
                        </a>
                    </li>
                @endif

            </ul>
        </div>
    </div>
</div>