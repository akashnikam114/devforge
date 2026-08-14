@extends('admin.layouts.app')
@section('content')
    <div class="nk-content-body">
        <div class="nk-block-head nk-block-head-sm">
            <div class="nk-block-between">
                <div class="nk-block-head-content">
                    <h3 class="nk-block-title page-title">App Release</h3>
                    <div class="nk-block-des">
                        <p>List of all app version releases</p>
                    </div>
                </div>
                <div class="nk-block-head-content">
                    <div class="toggle-wrap nk-block-tools-toggle">
                        <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                            <em class="icon ni ni-menu-alt-r"></em>
                        </a>
                        <div class="toggle-expand-content" data-content="pageMenu">
                            <ul class="nk-block-tools g-3">
                                <li class="nk-block-tools-opt">
                                    <a href="{{ url('admin/app_releases/add') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus"></em>
                                        <span>Add App Release</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="nk-block nk-block-lg mt-3">
            <div class="card card-bordered card-preview">
                <div class="card-inner">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Platform</th>
                                <th>Latest Version</th>
                                <th>Force Update</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            datatable1();
        });

        function datatable1() {
            NioApp.DataTable('#myTable', {
                "processing": true,
                "serverSide": true,
                "searching": true,
                "bLengthChange": true,
                ajax: "{{ route('admin.app_releases') }}",
                "order": [[0, "desc"]],
                responsive: !0,
                autoFill: !0,
                keys: !0,
                lengthMenu: [
                    [10, 100, 500, -1],
                    [10, 100, 500, "All"]
                ],
                "columns": [
                    {
                        "mData": "platform",
                        render: function(data) {
                            if (data === 'ios') {
                                return "<span>iOS</span>";
                            }
                            if (data === 'android') {
                                return "<span>Android</span>";
                            }
                            return data;
                        }
                    },
                    {
                        "mData": "latest_version"
                    },
                    {
                        "mData": "is_force_update"
                    },
                    {
                        "mData": "action"
                    }
                ]
            });
        }

        function deleteRecord(id) {
            if ($.trim(id)) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to delete this?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                            url: "{{ url('admin/app_releases/delete') }}/" + id,
                            type: "DELETE",
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire("Deleted", response.message, "success");
                                    $("#myTable").DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire("Error!", response.message, "error");
                                }
                            },
                            error: function() {
                                Swal.fire("Error!", "Something went wrong", "error");
                            }
                        });
                    }
                });
            }
        }
    </script>
@endsection
