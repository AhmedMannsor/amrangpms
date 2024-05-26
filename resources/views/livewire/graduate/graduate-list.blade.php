<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <!--begin::Card title-->
        <div class="card-title">
            <!--begin::Search-->
            <div class="d-flex align-items-center position-relative my-1">
                {!! getIcon('magnifier', 'fs-3 position-absolute ms-5') !!}
                <input type="text" wire:model.live="search" class="form-control form-control-solid w-250px ps-13"
                       placeholder="{{trans()->get('_user-list.Search user')}}" id="mySearchInput"/>
            </div>

            <!--end::Search-->
        </div>
        <!--begin::Card title-->
        <!--begin::Card toolbar-->
        <div class="card-toolbar">
            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                <!--begin::Filter-->
                <x-filter-roles :$roles/>
                <!--end::Filter-->

                <!--begin::Export-->
                <x-export-dropdown/>
                <!--end::Export-->
                <!--begin::Add user-->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_add_graduate">
                    {!! getIcon('plus', 'fs-2', '', 'i') !!}
                    {{trans()->get('_user-list.Add User')}}
                </button>
                <!--end::Add user-->
            </div>
            <!--end::Toolbar-->
            <div class="d-flex justify-content-end align-items-center d-none" data-kt-user-table-toolbar="selected">
                <div class="fw-bold me-5">
                    <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                </div>
                <button type="button" class="btn btn-danger" data-kt-user-table-selected="delete_selected">Delete
                    Selected
                </button>
                <button type="button" wire:click="update_status" class="btn btn-light-primary me-3"
                        data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-pencil fs-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i> تعديل الحالة
                </button>

            </div>
            <!--begin::Modal-->
            <!--end::Modal-->
        </div>
        <!--end::Card toolbar-->
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <!--begin::Table-->
        <table id="users-table" class="table table-responsive align-middle table-row-dashed fs-6 gy-5">
            <thead>
            <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                <th class="w-10px pe-2">
                    <x-check-all/>
                </th>
                <th class=" text-start min-w-125px">{{trans('_graduate_table.Graduate')}}</th>
                <th class=" text-start min-w-125px">{{trans('_graduate_table.Department')}}</th>
                <th class="min-w-125px">{{trans('_graduate_table.Project')}}</th>
                <th class=" text-start min-w-125px">{{trans('_graduate_table.For Year')}}</th>
                <th class=" text-start min-w-125px">{{trans('_graduate_table.Joined Date')}}</th>
                <th class="text-start min-w-100px">{{trans('_graduate_table.ACTION')}}</th>
            </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">

            @foreach($graduates as $graduate)

                <tr wire:key="{{ $graduate->id }}">

                    <td>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" wire:model="selectedUserIds"
                                   value="{{$graduate->id}}"/>
                        </div>
                    </td>

                    <td class="d-flex align-items-center">
                        @include('pages/apps.user-management.graduates.columns._user')
                    </td>
                    <td>{{ucwords($graduate->department->name?? '')}}</td>
                    @if($graduate->project)
                        <td class="whitespace-nowrap p-3 text-sm">
                            <div
                                class="py-0.5 pl-2 pr-1 inline-flex font-medium items-center gap-1 text-green-600 text-xs  opacity-75">

                                <span class="badge badge-light-success fw-bold">{{$graduate->project->title}}   </span>
                            </div>
                        </td>
                    @else

                        <td class="whitespace-nowrap p-3 text-xs">
                            <div
                                class=" py-0.5 pl-2 pr-1 inline-flex font-medium items-center gap-1 text-red-600 text-xs opacity-75">

                                <span class="badge badge-light-danger fw-bold">لم يسجل بعد</span>

                            </div>
                        </td>
                    @endif
                    <td>
                        <div class="badge badge-light fw-bold">
                            {{$graduate->for_year}}
                        </div>
                    </td>
                    <td>{{$graduate->created_at->format('d M Y, h:i a')}}
                    </td>
                    <td class=" d-flex align-items-center">
                        {{--                        <x-action-dropdown :$graduate/>--}}
                        @include('pages/apps.user-management.graduates.columns._actions')

                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
        <div class="pt-4  justify-between items-center">
            {!! $graduates->links('livewire.user.pagination')  !!}
        </div>

    </div>
</div>
{{addJavascriptFile('assets/js/custom/apps/user-management/users/list/table1.js')}}
{{addJavascriptFile('assets/js/custom/apps/user-management/users/list/tailwindcss.js')}}
{{--<script src="//unpkg.com/alpinejs" defer></script>--}}
@push('scripts')
    <script>
        KTMenu.init();
        document.querySelectorAll('[data-kt-action="delete_row"]').forEach(function (element) {
            element.addEventListener('click', function () {
                Swal.fire({
                    text: 'Are you sure you want to remove?',
                    icon: 'warning',
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-secondary',
                    }
                }).then((result) => {
                    if (result.value) {
                        Livewire.dispatch('delete_user', [this.getAttribute('data-kt-user-id')]);
                    }
                });
            });
        });
        // var table = document.getElementById('kt_table_users');
        document.querySelectorAll('[data-kt-action="update_row"]').forEach(function (element) {
            element.addEventListener('click', function () {
                Livewire.dispatch('update_user', [this.getAttribute('data-kt-user-id')]);
            });
        });
        // Add click event listener to delete buttons

    </script>
@endpush

